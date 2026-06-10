package com.patelhardware.servlet;

import com.patelhardware.util.DBConnection;

import javax.json.Json;
import javax.json.JsonArrayBuilder;
import javax.json.JsonObjectBuilder;
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

public class ViewCartItemsServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("userId") == null) {
            response.sendRedirect("login.html");
            return;
        }

        int userId = (int) session.getAttribute("userId");
        String accept = request.getHeader("Accept");

        // JSON response
        if (accept != null && accept.contains("application/json")) {
            response.setContentType("application/json");
            response.setCharacterEncoding("UTF-8");
            PrintWriter out = response.getWriter();

            JsonArrayBuilder arrayBuilder = Json.createArrayBuilder();
            try (Connection conn = DBConnection.getConnection()) {
                String sql = "SELECT c.cart_id, c.quantity, i.item_id, i.name, i.color, i.description, i.price "
                        + "FROM cart c JOIN items i ON c.item_id = i.item_id WHERE c.user_id = ?";
                PreparedStatement stmt = conn.prepareStatement(sql);
                stmt.setInt(1, userId);
                ResultSet rs = stmt.executeQuery();
                while (rs.next()) {
                    JsonObjectBuilder obj = Json.createObjectBuilder()
                            .add("cartId", rs.getInt("cart_id"))
                            .add("itemId", rs.getInt("item_id"))
                            .add("name", rs.getString("name"))
                            .add("color", rs.getString("color"))
                            .add("description", rs.getString("description"))
                            .add("price", rs.getDouble("price"))
                            .add("quantity", rs.getInt("quantity"));
                    arrayBuilder.add(obj);
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            out.print(arrayBuilder.build().toString());
            return;
        }

        // HTML response
        response.setContentType("text/html");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        out.println("<!DOCTYPE html>");
        out.println("<html lang='en'>");
        out.println("<head>");
        out.println("  <meta charset='UTF-8'>");
        out.println("  <meta name='viewport' content='width=device-width, initial-scale=1.0'>");
        out.println("  <title>View Cart - Patel Hardware</title>");
        out.println("  <style>");
        out.println("    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f0f0f0; }");
        out.println("    .navbar { background-color: #0066cc; padding: 15px; overflow: hidden; }");
        out.println("    .navbar a { color: white; text-decoration: none; padding: 10px 20px; font-weight: bold; font-size: 16px; }");
        out.println("    .navbar a:hover { background-color: #004999; border-radius: 4px; }");
        out.println("    .navbar input[type='text'] { float: right; padding: 8px; border: none; border-radius: 4px; font-size: 14px; width: 250px; }");
        out.println("    .navbar button { float: right; padding: 8px 12px; background-color: white; border: none; cursor: pointer; border-radius: 4px; margin-right: 5px; }");
        out.println("    .content { padding: 30px; text-align: center; }");
        out.println("    h1 { color: #006600; }");
        out.println("    table { margin: 20px auto; border-collapse: collapse; border: 2px solid #00cccc; }");
        out.println("    th, td { padding: 10px 15px; text-align: left; border: 1px solid #ddd; }");
        out.println("    th { background-color: #e0f7fa; font-weight: bold; }");
        out.println("    .total { font-weight: bold; font-size: 18px; margin-top: 15px; }");
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
        out.println("    <h1>Your Cart</h1>");

        out.println("    <table>");
        out.println("      <tr><th>ITEM#</th><th>NAME</th><th>COLOR</th><th>DESCRIPTION</th><th>PRICE</th><th>QUANTITY</th><th>SUBTOTAL</th></tr>");

        double total = 0;
        try (Connection conn = DBConnection.getConnection()) {
            String sql = "SELECT c.quantity, i.item_id, i.name, i.color, i.description, i.price "
                    + "FROM cart c JOIN items i ON c.item_id = i.item_id WHERE c.user_id = ?";
            PreparedStatement stmt = conn.prepareStatement(sql);
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                int itemId = rs.getInt("item_id");
                String name = rs.getString("name");
                String color = rs.getString("color");
                String description = rs.getString("description");
                double price = rs.getDouble("price");
                int quantity = rs.getInt("quantity");
                double subtotal = price * quantity;
                total += subtotal;

                out.println("      <tr>");
                out.println("        <td>" + itemId + "</td>");
                out.println("        <td>" + name + "</td>");
                out.println("        <td>" + color + "</td>");
                out.println("        <td>" + description + "</td>");
                out.println("        <td>R" + String.format("%.0f", price) + "</td>");
                out.println("        <td>" + quantity + "</td>");
                out.println("        <td>R" + String.format("%.0f", subtotal) + "</td>");
                out.println("      </tr>");
            }
        } catch (Exception e) {
            out.println("      <tr><td colspan='7'>Error loading cart: " + e.getMessage() + "</td></tr>");
            e.printStackTrace();
        }

        out.println("    </table>");
        out.println("    <p class='total'>Total: R" + String.format("%.0f", total) + "</p>");
        out.println("    <p><a href='main'>Go back to Main Menu</a></p>");
        out.println("  </div>");
        out.println("</body>");
        out.println("</html>");
    }
}
