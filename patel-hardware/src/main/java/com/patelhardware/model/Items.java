package com.patelhardware.model;

public class Items {

    private int itemId;
    private String name;
    private String color;
    private String description;
    private double price;
    private String available;

    public Items() {
    }

    public Items(int itemId, String name, String color, String description, double price, String available) {
        this.itemId = itemId;
        this.name = name;
        this.color = color;
        this.description = description;
        this.price = price;
        this.available = available;
    }

    public int getItemId() {
        return itemId;
    }

    public void setItemId(int itemId) {
        this.itemId = itemId;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getColor() {
        return color;
    }

    public void setColor(String color) {
        this.color = color;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public double getPrice() {
        return price;
    }

    public void setPrice(double price) {
        this.price = price;
    }

    public String getAvailable() {
        return available;
    }

    public void setAvailable(String available) {
        this.available = available;
    }
}
