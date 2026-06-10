package com.patelhardware.servlet;

import com.patelhardware.model.LoginResponse;
import com.patelhardware.util.DBConnection;

import javax.json.Json;
import javax.json.JsonObject;
import javax.json.JsonReader;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

public class LoginServlet extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        String username = request.getParameter("username");
        String password = request.getParameter("password");

        // If parameters are null, try reading JSON body
        if (username == null || password == null) {
            try (JsonReader reader = Json.createReader(request.getInputStream())) {
                JsonObject jsonObject = reader.readObject();
                username = jsonObject.getString("username", null);
                password = jsonObject.getString("password", null);
            } catch (Exception e) {
                // parameters remain null
            }
        }

        LoginResponse loginResponse = new LoginResponse();

        if (username == null || password == null || username.isEmpty() || password.isEmpty()) {
            loginResponse.setSuccess(false);
            loginResponse.setMessage("Username and password are required.");
            out.print(toJson(loginResponse));
            return;
        }

        try (Connection conn = DBConnection.getConnection()) {
            String sql = "SELECT * FROM users WHERE username = ? AND password = ?";
            PreparedStatement stmt = conn.prepareStatement(sql);
            stmt.setString(1, username);
            stmt.setString(2, password);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                HttpSession session = request.getSession();
                session.setAttribute("userId", rs.getInt("user_id"));
                session.setAttribute("username", rs.getString("username"));

                loginResponse.setSuccess(true);
                loginResponse.setMessage("Login successful.");
                loginResponse.setUsername(rs.getString("username"));

                // Redirect to main page for form-based login
                response.setContentType("text/html");
                response.sendRedirect("main");
                return;
            } else {
                loginResponse.setSuccess(false);
                loginResponse.setMessage("Invalid username or password.");
            }
        } catch (Exception e) {
            loginResponse.setSuccess(false);
            loginResponse.setMessage("Database error: " + e.getMessage());
            e.printStackTrace();
        }

        out.print(toJson(loginResponse));
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.sendRedirect("login.html");
    }

    private String toJson(LoginResponse lr) {
        return Json.createObjectBuilder()
                .add("success", lr.isSuccess())
                .add("message", lr.getMessage() != null ? lr.getMessage() : "")
                .add("username", lr.getUsername() != null ? lr.getUsername() : "")
                .build().toString();
    }
}
