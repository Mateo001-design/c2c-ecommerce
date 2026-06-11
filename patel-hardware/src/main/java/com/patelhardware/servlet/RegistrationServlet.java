package com.patelhardware.servlet;

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
import java.sql.SQLException;

// RegistrationServlet handles new user registration
public class RegistrationServlet extends HttpServlet {

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

            // Insert new user into the database
            String sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            pstmt.setString(1, username);
            pstmt.setString(2, password);

            int rowsInserted = pstmt.executeUpdate();

            if (rowsInserted > 0) {
                // Registration successful
                JsonObject jsonResponse = Json.createObjectBuilder()
                        .add("success", true)
                        .add("message", "Registration successful! You can now login.")
                        .build();
                out.print(jsonResponse.toString());
            } else {
                // Registration failed
                JsonObject jsonResponse = Json.createObjectBuilder()
                        .add("success", false)
                        .add("message", "Registration failed. Please try again.")
                        .build();
                out.print(jsonResponse.toString());
            }

            pstmt.close();
            conn.close();

        } catch (SQLException e) {
            // Handle duplicate username or other database errors
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Username already exists. Please choose another.")
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }
}
