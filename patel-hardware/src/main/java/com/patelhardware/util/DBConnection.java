package com.patelhardware.util;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

// Utility class for database connection
public class DBConnection {

    // Database connection details
    private static final String URL = "jdbc:mysql://localhost:3306/patel_hardware";
    private static final String USER = "root";
    private static final String PASSWORD = "";

    // Method to get a connection to the database
    public static Connection getConnection() throws SQLException {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        }
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }
}
