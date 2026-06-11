package com.patelhardware.servlet;

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

// LoginServlet handles user login requests
public class LoginServlet extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        // Read JSON from request body
        JsonReader jsonReader = Json.createReader(request.getReader());
        JsonObject jsonInput = jsonReader.readObject();
        jsonReader.close();

        String username = jsonInput.getString("username");
        String password = jsonInput.getString("password");

        try {
            Connection conn = DBConnection.getConnection();

            // Query the database to check credentials
            String sql = "SELECT * FROM users WHERE username = ? AND password = ?";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            pstmt.setString(1, username);
            pstmt.setString(2, password);

            ResultSet rs = pstmt.executeQuery();

            if (rs.next()) {
                // Login successful - create session
                HttpSession session = request.getSession();
                session.setAttribute("userId", rs.getInt("user_id"));
                session.setAttribute("username", username);

                // Build success JSON response
                JsonObject jsonResponse = Json.createObjectBuilder()
                        .add("success", true)
                        .add("message", "Login successful")
                        .add("username", username)
                        .build();
                out.print(jsonResponse.toString());
            } else {
                // Login failed
                JsonObject jsonResponse = Json.createObjectBuilder()
                        .add("success", false)
                        .add("message", "Invalid username or password")
                        .add("username", "")
                        .build();
                out.print(jsonResponse.toString());
            }

            rs.close();
            pstmt.close();
            conn.close();

        } catch (Exception e) {
            // Handle database errors
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Server error: " + e.getMessage())
                    .add("username", "")
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }
}
