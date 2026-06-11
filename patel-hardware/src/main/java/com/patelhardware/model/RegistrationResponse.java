package com.patelhardware.model;

// RegistrationResponse class to represent the response after registration attempt
public class RegistrationResponse {

    private boolean success;
    private String message;

    // Default constructor
    public RegistrationResponse() {
    }

    // Constructor with parameters
    public RegistrationResponse(boolean success, String message) {
        this.success = success;
        this.message = message;
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
}
