package com.patelhardware.model;

// LoginResponse class to represent the response after login attempt
public class LoginResponse {

    private boolean success;
    private String message;
    private String username;

    // Default constructor
    public LoginResponse() {
    }

    // Constructor with parameters
    public LoginResponse(boolean success, String message, String username) {
        this.success = success;
        this.message = message;
        this.username = username;
    }

    // Getters and Setters
    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getUsername() {
        return username;
    }

    public void setUsername(String username) {
        this.username = username;
    }
}
