package com.patelhardware.servlet;

import javax.json.Json;
import javax.json.JsonObject;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;
import java.io.PrintWriter;

// MainServlet handles the main menu page and session validation
public class MainServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        // Check if user is logged in
        HttpSession session = request.getSession(false);

        if (session != null && session.getAttribute("username") != null) {
            String username = (String) session.getAttribute("username");

            // Return the main menu options as JSON
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("loggedIn", true)
                    .add("username", username)
                    .add("message", "Welcome to the Patel hardware web application.")
                    .build();
            out.print(jsonResponse.toString());
        } else {
            // User not logged in
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("loggedIn", false)
                    .add("username", "")
                    .add("message", "Please login first.")
                    .build();
            response.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
            out.print(jsonResponse.toString());
        }

        out.flush();
    }

    // Handle logout
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        // Invalidate the session to log out
        HttpSession session = request.getSession(false);
        if (session != null) {
            session.invalidate();
        }

        JsonObject jsonResponse = Json.createObjectBuilder()
                .add("success", true)
                .add("message", "Logged out successfully")
                .build();
        out.print(jsonResponse.toString());
        out.flush();
    }
}
