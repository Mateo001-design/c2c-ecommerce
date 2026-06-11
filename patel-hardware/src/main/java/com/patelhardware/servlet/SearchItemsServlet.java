package com.patelhardware.servlet;

import com.patelhardware.util.DBConnection;

import javax.json.Json;
import javax.json.JsonArrayBuilder;
import javax.json.JsonObject;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

// SearchItemsServlet handles searching items by name or description
public class SearchItemsServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        // Get the search query from the request parameter
        String searchQuery = request.getParameter("query");

        if (searchQuery == null || searchQuery.trim().isEmpty()) {
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Please enter a search term")
                    .build();
            out.print(jsonResponse.toString());
            out.flush();
            return;
        }

        try {
            Connection conn = DBConnection.getConnection();

            // Search by name or description using LIKE
            String sql = "SELECT * FROM items WHERE name LIKE ? OR description LIKE ?";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            pstmt.setString(1, "%" + searchQuery + "%");
            pstmt.setString(2, "%" + searchQuery + "%");

            ResultSet rs = pstmt.executeQuery();

            // Build JSON array of matching items
            JsonArrayBuilder resultsArray = Json.createArrayBuilder();

            while (rs.next()) {
                JsonObject item = Json.createObjectBuilder()
                        .add("itemId", rs.getInt("item_id"))
                        .add("name", rs.getString("name"))
                        .add("color", rs.getString("color"))
                        .add("description", rs.getString("description"))
                        .add("price", rs.getDouble("price"))
                        .add("available", rs.getString("available"))
                        .build();
                resultsArray.add(item);
            }

            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", true)
                    .add("results", resultsArray)
                    .add("searchTerm", searchQuery)
                    .build();
            out.print(jsonResponse.toString());

            rs.close();
            pstmt.close();
            conn.close();

        } catch (Exception e) {
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Search error: " + e.getMessage())
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }
}
