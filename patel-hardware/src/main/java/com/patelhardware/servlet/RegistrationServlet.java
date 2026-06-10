package com.patelhardware.servlet;

import com.patelhardware.model.RegistrationResponse;
import com.patelhardware.util.DBConnection;

import javax.json.Json;
import javax.json.JsonObject;
import javax.json.JsonReader;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

public class RegistrationServlet extends HttpServlet {

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

        RegistrationResponse regResponse = new RegistrationResponse();

        if (username == null || password == null || username.isEmpty() || password.isEmpty()) {
            regResponse.setSuccess(false);
            regResponse.setMessage("Username and password are required.");
            out.print(toJson(regResponse));
            return;
        }

        try (Connection conn = DBConnection.getConnection()) {
            // Check if username already exists
            String checkSql = "SELECT * FROM users WHERE username = ?";
            PreparedStatement checkStmt = conn.prepareStatement(checkSql);
            checkStmt.setString(1, username);
            ResultSet rs = checkStmt.executeQuery();

            if (rs.next()) {
                regResponse.setSuccess(false);
                regResponse.setMessage("Username already exists.");
                out.print(toJson(regResponse));
                return;
            }

            // Insert new user
            String insertSql = "INSERT INTO users (username, password) VALUES (?, ?)";
            PreparedStatement insertStmt = conn.prepareStatement(insertSql);
            insertStmt.setString(1, username);
            insertStmt.setString(2, password);
            int rowsInserted = insertStmt.executeUpdate();

            if (rowsInserted > 0) {
                regResponse.setSuccess(true);
                regResponse.setMessage("Registration successful.");

                // Redirect to login page for form-based registration
                response.setContentType("text/html");
                response.sendRedirect("login.html");
                return;
            } else {
                regResponse.setSuccess(false);
                regResponse.setMessage("Registration failed. Please try again.");
            }
        } catch (Exception e) {
            regResponse.setSuccess(false);
            regResponse.setMessage("Database error: " + e.getMessage());
            e.printStackTrace();
        }

        out.print(toJson(regResponse));
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.sendRedirect("register.html");
    }

    private String toJson(RegistrationResponse rr) {
        return Json.createObjectBuilder()
                .add("success", rr.isSuccess())
                .add("message", rr.getMessage() != null ? rr.getMessage() : "")
                .build().toString();
    }
}
