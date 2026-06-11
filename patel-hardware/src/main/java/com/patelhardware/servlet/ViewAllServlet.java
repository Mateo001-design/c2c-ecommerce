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

// ViewAllServlet retrieves and returns all items from the database
public class ViewAllServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        try {
            Connection conn = DBConnection.getConnection();

            // Query all items from the database
            String sql = "SELECT * FROM items";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            ResultSet rs = pstmt.executeQuery();

            // Build JSON array of items
            JsonArrayBuilder itemsArray = Json.createArrayBuilder();

            while (rs.next()) {
                JsonObject item = Json.createObjectBuilder()
                        .add("itemId", rs.getInt("item_id"))
                        .add("name", rs.getString("name"))
                        .add("color", rs.getString("color"))
                        .add("description", rs.getString("description"))
                        .add("price", rs.getDouble("price"))
                        .add("available", rs.getString("available"))
                        .build();
                itemsArray.add(item);
            }

            // Build final response
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", true)
                    .add("items", itemsArray)
                    .build();
            out.print(jsonResponse.toString());

            rs.close();
            pstmt.close();
            conn.close();

        } catch (Exception e) {
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Error retrieving items: " + e.getMessage())
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }
}
