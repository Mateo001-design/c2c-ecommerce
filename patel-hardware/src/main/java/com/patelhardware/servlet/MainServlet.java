package com.patelhardware.servlet;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;
import java.io.PrintWriter;

public class MainServlet extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("username") == null) {
            response.sendRedirect("login.html");
            return;
        }

        String username = (String) session.getAttribute("username");

        response.setContentType("text/html");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();

        out.println("<!DOCTYPE html>");
        out.println("<html lang='en'>");
        out.println("<head>");
        out.println("  <meta charset='UTF-8'>");
        out.println("  <meta name='viewport' content='width=device-width, initial-scale=1.0'>");
        out.println("  <title>Patel Hardware - Home</title>");
        out.println("  <style>");
        out.println("    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f0f0f0; }");
        out.println("    .navbar { background-color: #0066cc; padding: 15px; overflow: hidden; }");
        out.println("    .navbar a { color: white; text-decoration: none; padding: 10px 20px; font-weight: bold; font-size: 16px; }");
        out.println("    .navbar a:hover { background-color: #004999; border-radius: 4px; }");
        out.println("    .navbar input[type='text'] { float: right; padding: 8px; border: none; border-radius: 4px; font-size: 14px; width: 250px; }");
        out.println("    .navbar button { float: right; padding: 8px 12px; background-color: white; border: none; cursor: pointer; border-radius: 4px; margin-right: 5px; }");
        out.println("    .content { padding: 30px; text-align: center; }");
        out.println("    h1 { color: #006600; }");
        out.println("    .menu-table { margin: 20px auto; border: 2px solid #00cccc; padding: 20px; }");
        out.println("    .menu-table a { color: #006666; text-decoration: none; font-size: 18px; padding: 10px 30px; display: inline-block; }");
        out.println("    .menu-table a:hover { text-decoration: underline; }");
        out.println("    .logout-btn { padding: 8px 30px; border: 1px solid #999; background-color: #f0f0f0; cursor: pointer; font-size: 14px; margin-top: 10px; }");
        out.println("  </style>");
        out.println("</head>");
        out.println("<body>");

        // Navigation bar
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

        // Main content
        out.println("  <div class='content'>");
        out.println("    <h1>Patel Hardware</h1>");
        out.println("    <p><b>Welcome to the Patel hardware web application. Please select one of the options below:</b></p>");
        out.println("    <table class='menu-table'>");
        out.println("      <tr>");
        out.println("        <td><a href='main'>Main Menu</a></td>");
        out.println("        <td><a href='viewall'>View all Items</a></td>");
        out.println("      </tr>");
        out.println("      <tr>");
        out.println("        <td><a href='buyitem'>Buy an Item</a></td>");
        out.println("        <td><a href='viewcart'>View Cart</a></td>");
        out.println("      </tr>");
        out.println("      <tr>");
        out.println("        <td colspan='2'><form action='logout' method='post'><button class='logout-btn' type='submit'>Logout</button></form></td>");
        out.println("      </tr>");
        out.println("    </table>");
        out.println("  </div>");

        out.println("</body>");
        out.println("</html>");
    }
}
