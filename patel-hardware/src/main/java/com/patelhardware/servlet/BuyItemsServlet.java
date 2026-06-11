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

// BuyItemsServlet handles adding items to the user's cart
public class BuyItemsServlet extends HttpServlet {

    // GET method to retrieve available items for purchase
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        try {
            Connection conn = DBConnection.getConnection();

            // Get available items
            String sql = "SELECT * FROM items WHERE available = 'Yes'";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            ResultSet rs = pstmt.executeQuery();

            javax.json.JsonArrayBuilder itemsArray = Json.createArrayBuilder();

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
                    .add("message", "Error: " + e.getMessage())
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }

    // POST method to add an item to the cart
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        // Check if user is logged in
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("userId") == null) {
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Please login first")
                    .build();
            response.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
            out.print(jsonResponse.toString());
            out.flush();
            return;
        }

        int userId = (int) session.getAttribute("userId");

        // Read JSON request body
        JsonReader jsonReader = Json.createReader(request.getReader());
        JsonObject jsonInput = jsonReader.readObject();
        jsonReader.close();

        int itemId = jsonInput.getInt("itemId");
        int quantity = jsonInput.getInt("quantity", 1);

        try {
            Connection conn = DBConnection.getConnection();

            // Insert item into cart
            String sql = "INSERT INTO cart (user_id, item_id, quantity) VALUES (?, ?, ?)";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            pstmt.setInt(1, userId);
            pstmt.setInt(2, itemId);
            pstmt.setInt(3, quantity);

            int rowsInserted = pstmt.executeUpdate();

            if (rowsInserted > 0) {
                JsonObject jsonResponse = Json.createObjectBuilder()
                        .add("success", true)
                        .add("message", "Item added to cart successfully!")
                        .build();
                out.print(jsonResponse.toString());
            } else {
                JsonObject jsonResponse = Json.createObjectBuilder()
                        .add("success", false)
                        .add("message", "Failed to add item to cart")
                        .build();
                out.print(jsonResponse.toString());
            }

            pstmt.close();
            conn.close();

        } catch (Exception e) {
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Error: " + e.getMessage())
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }
}
