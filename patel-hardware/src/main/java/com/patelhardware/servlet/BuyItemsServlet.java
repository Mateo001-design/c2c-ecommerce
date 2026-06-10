package com.patelhardware.servlet;

import com.patelhardware.util.DBConnection;

import javax.json.Json;
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

public class BuyItemsServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("username") == null) {
            response.sendRedirect("login.html");
            return;
        }

        response.setContentType("text/html");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        out.println("<!DOCTYPE html>");
        out.println("<html lang='en'>");
        out.println("<head>");
        out.println("  <meta charset='UTF-8'>");
        out.println("  <meta name='viewport' content='width=device-width, initial-scale=1.0'>");
        out.println("  <title>Buy an Item - Patel Hardware</title>");
        out.println("  <style>");
        out.println("    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f0f0f0; }");
        out.println("    .navbar { background-color: #0066cc; padding: 15px; overflow: hidden; }");
        out.println("    .navbar a { color: white; text-decoration: none; padding: 10px 20px; font-weight: bold; font-size: 16px; }");
        out.println("    .navbar a:hover { background-color: #004999; border-radius: 4px; }");
        out.println("    .navbar input[type='text'] { float: right; padding: 8px; border: none; border-radius: 4px; font-size: 14px; width: 250px; }");
        out.println("    .navbar button { float: right; padding: 8px 12px; background-color: white; border: none; cursor: pointer; border-radius: 4px; margin-right: 5px; }");
        out.println("    .content { padding: 30px; text-align: center; }");
        out.println("    h1 { color: #006600; }");
        out.println("    .form-container { max-width: 500px; margin: 20px auto; text-align: left; }");
        out.println("    label { font-weight: bold; display: block; margin-top: 10px; }");
        out.println("    input[type='number'], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }");
        out.println("    .submit-btn { margin-top: 15px; padding: 10px 30px; background-color: #cc0000; color: white; border: none; cursor: pointer; font-size: 16px; }");
        out.println("  </style>");
        out.println("</head>");
        out.println("<body>");

        // Navbar
        out.println("  <div class='navbar'>");
        out.println("    <a href='main'>Home</a>");
        out.println("    <a href='viewall'>View All Items</a>");
        out.println("    <a href='buyitem'>Buy an Item</a>");
        out.println("    <a href='viewcart'>View Cart</a>");
        out.println("    <form action='search' method='get' style='float:right;'>");
        out.println("      <button type='submit'>&#128269;</button>");
        out.println("      <input type='text' name='query' placeholder='Search Items'>");
        out.println("    </form>");
        out.println("  </div>");

        out.println("  <div class='content'>");
        out.println("    <h1>Buy an Item</h1>");
        out.println("    <p>Select an item and quantity to add to your cart.</p>");

        out.println("    <div class='form-container'>");
        out.println("      <form action='buyitem' method='post'>");
        out.println("        <label>Select Item:</label>");
        out.println("        <select name='itemId' required>");
        out.println("          <option value=''>-- Select an item --</option>");

        try (Connection conn = DBConnection.getConnection()) {
            String sql = "SELECT item_id, name, price FROM items WHERE available = 'Yes'";
            PreparedStatement stmt = conn.prepareStatement(sql);
            ResultSet rs = stmt.executeQuery();
            while (rs.next()) {
                int itemId = rs.getInt("item_id");
                String name = rs.getString("name");
                double price = rs.getDouble("price");
                out.println("          <option value='" + itemId + "'>" + name + " - R" + String.format("%.0f", price) + "</option>");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        out.println("        </select>");
        out.println("        <label>Quantity:</label>");
        out.println("        <input type='number' name='quantity' value='1' min='1' required>");
        out.println("        <br><button class='submit-btn' type='submit'>Add to Cart</button>");
        out.println("      </form>");
        out.println("    </div>");

        out.println("  </div>");
        out.println("</body>");
        out.println("</html>");
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("userId") == null) {
            response.sendRedirect("login.html");
            return;
        }

        int userId = (int) session.getAttribute("userId");
        String itemIdStr = request.getParameter("itemId");
        String quantityStr = request.getParameter("quantity");

        int itemId;
        int quantity;
        try {
            itemId = Integer.parseInt(itemIdStr);
            quantity = (quantityStr != null && !quantityStr.isEmpty()) ? Integer.parseInt(quantityStr) : 1;
        } catch (NumberFormatException e) {
            response.sendRedirect("buyitem");
            return;
        }

        try (Connection conn = DBConnection.getConnection()) {
            String sql = "INSERT INTO cart (user_id, item_id, quantity) VALUES (?, ?, ?)";
            PreparedStatement stmt = conn.prepareStatement(sql);
            stmt.setInt(1, userId);
            stmt.setInt(2, itemId);
            stmt.setInt(3, quantity);
            stmt.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        }

        response.sendRedirect("viewcart");
    }
}
