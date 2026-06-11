package com.patelhardware.servlet;

import com.patelhardware.util.DBConnection;

import javax.json.Json;
import javax.json.JsonArrayBuilder;
import javax.json.JsonObject;
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

// ViewCartItemsServlet retrieves all cart items for the logged-in user
public class ViewCartItemsServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
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

        try {
            Connection conn = DBConnection.getConnection();

            // Join cart with items to get item details
            String sql = "SELECT c.cart_id, c.quantity, i.item_id, i.name, i.color, " +
                         "i.description, i.price, i.available " +
                         "FROM cart c JOIN items i ON c.item_id = i.item_id " +
                         "WHERE c.user_id = ?";
            PreparedStatement pstmt = conn.prepareStatement(sql);
            pstmt.setInt(1, userId);
            ResultSet rs = pstmt.executeQuery();

            // Build JSON array of cart items
            JsonArrayBuilder cartArray = Json.createArrayBuilder();
            double totalPrice = 0;

            while (rs.next()) {
                int quantity = rs.getInt("quantity");
                double price = rs.getDouble("price");
                double subtotal = price * quantity;
                totalPrice += subtotal;

                JsonObject cartItem = Json.createObjectBuilder()
                        .add("cartId", rs.getInt("cart_id"))
                        .add("itemId", rs.getInt("item_id"))
                        .add("name", rs.getString("name"))
                        .add("color", rs.getString("color"))
                        .add("description", rs.getString("description"))
                        .add("price", price)
                        .add("quantity", quantity)
                        .add("subtotal", subtotal)
                        .build();
                cartArray.add(cartItem);
            }

            // Build response with cart items and total
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", true)
                    .add("cartItems", cartArray)
                    .add("totalPrice", totalPrice)
                    .build();
            out.print(jsonResponse.toString());

            rs.close();
            pstmt.close();
            conn.close();

        } catch (Exception e) {
            JsonObject jsonResponse = Json.createObjectBuilder()
                    .add("success", false)
                    .add("message", "Error retrieving cart: " + e.getMessage())
                    .build();
            out.print(jsonResponse.toString());
        }

        out.flush();
    }
}
