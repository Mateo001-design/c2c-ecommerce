# Deliverable 1 – Project Proposal
## iTradeZA: A Consumer-to-Consumer (C2C) E-Commerce Platform for South Africa

---

## 1.1 Introduction

South Africa's e-commerce sector is experiencing unprecedented growth, with online retail turnover projected to exceed R130 billion by the end of 2025 (World Wide Worx & Mastercard, 2025). While formal Business-to-Consumer (B2C) platforms such as Takealot dominate the market, a significant opportunity exists in the Consumer-to-Consumer (C2C) segment—particularly within the informal and township economies.

The informal sector accounts for approximately 20% of total employment in South Africa (Statistics South Africa, 2025), and the "Township Economy" is valued at roughly R900 billion annually (Standard Bank, 2025). Many informal traders and "side-hustlers" have begun migrating from traditional street vending to digital platforms, yet most still operate without formal digital infrastructure, secure payment integration, or verified identity systems.

**iTradeZA** is a proposed C2C e-commerce platform designed specifically for the South African context. It aims to bridge the gap between the informal economy and digitized marketplaces by providing a secure, low-data, mobile-responsive platform where individuals can buy and sell goods directly. The platform will support ZAR-denominated pricing, local payment gateways (EFT, e-Wallet, cash on delivery), seller identity verification, and a role-based administrative system—all built using HTML, CSS, JavaScript, PHP, and MySQL.

---

## 1.2 Needs / Problems

### Problem Statement

Despite the rapid digitisation of South African retail, C2C traders face several critical barriers that prevent them from fully participating in the digital economy:

1. **Lack of Localised C2C Platforms (5+ years)**
   Most available digital marketplaces (e.g., Gumtree, Facebook Marketplace, OLX) are international platforms that lack features tailored to South African township consumers. These platforms do not integrate with local payment systems (such as Capitec Pay or FNB e-Wallet), do not support low-data interfaces, and offer limited recourse for dispute resolution. This problem has persisted for over five years, with no domestically developed platform rising to fill the gap.

2. **Digital Trust Deficit**
   Research published in the *Global Media Journal* (2023) indicates that while township consumers are increasingly open to online shopping, their adoption is heavily influenced by perceived trust, usefulness, and convenience. Many users have experienced or heard of digital fraud, leading to a reluctance to transact online. There is no widely adopted local platform that offers verified seller identities and buyer protection for C2C trade.

3. **Absence of Integrated Delivery and Payment Logistics**
   Non-urban areas remain underserved by delivery networks. Existing platforms assume access to formal addresses and courier infrastructure, which many township traders and customers lack. Additionally, a large segment of the population is "under-banked" and relies on mobile money or cash transactions.

4. **Economic Marginalisation of Informal Traders**
   Informal traders who could benefit most from e-commerce access are precisely those with the fewest resources to build their own digital presence. Without a free, accessible, and user-friendly platform, millions of potential sellers remain locked out of the digital economy.

### Impact on Target Population

- **Target population:** Informal traders, side-hustlers, and individual consumers in South African townships and peri-urban areas, primarily aged 18–45.
- **Statistical context:** The Quarterly Labour Force Survey (QLFS Q1 2025) reports that the informal sector employs approximately 3.2 million people. Many of these individuals could expand their customer base and income through digital trade.
- **Surrounding impact:** Enabling C2C digital trade keeps transaction fees and revenues within South Africa, supports local economic development, and contributes to the broader goals of financial inclusion and digital skills development.

---

## 1.3 Goals / Objectives

| # | Goal / Objective | Measurable Target | Key Benefit |
|---|---|---|---|
| 1 | **Build a fully functional C2C e-commerce platform** | Deliver a hosted web application with registration, product listings, cart, checkout, messaging, and order management by end of Block 2, Week 5. | Provides a complete digital marketplace for individual buyers and sellers. |
| 2 | **Implement Role-Based Access Control (RBAC) admin panel** | Admin dashboard supports Create, Read, Update, Delete (CRUD) operations for at least 3 user roles (Admin, Seller, Buyer). | Ensures secure platform governance and multi-tier user management. |
| 3 | **Achieve mobile-responsive, low-data design** | All pages score above 80 on Google Lighthouse mobile audit; page load < 3 seconds on 3G connection. | Makes the platform accessible to users on affordable smartphones with limited data. |
| 4 | **Integrate South African payment methods** | Support at least 3 local payment options: EFT/bank transfer, e-Wallet (Capitec Pay/FNB), and cash on delivery. | Removes the barrier of requiring international credit cards or PayPal. |
| 5 | **Include seller verification and review system** | Sellers can be marked as "Verified" by admin; buyers can rate sellers (1–5 stars) after completed orders. | Builds trust and reduces fraud in the C2C ecosystem. |
| 6 | **Host the platform on a live server** | Platform is accessible via a public URL (InfinityFree or similar free hosting). | Meets submission requirements and demonstrates real-world deployment capability. |

---

## 1.4 Procedures / Scope of Work

### 1.4.1 Requirements Analysis
- Study the scenario and research the South African C2C e-commerce landscape.
- Identify key user personas: township seller, urban buyer, platform administrator.
- Define functional requirements (registration, listings, cart, checkout, messaging, admin CRUD).
- Define non-functional requirements (responsiveness, security, performance, accessibility).

### 1.4.2 Database Design
- Design the Enhanced Entity Relationship Diagram (EERD).
- Create the MySQL schema with the following core tables: `roles`, `users`, `categories`, `products`, `product_images`, `cart`, `orders`, `order_items`, `messages`, `reviews`.
- Normalise to 3NF; implement foreign key constraints and indexes.

### 1.4.3 System Design
- Produce Class Responsibility Collaborator (CRC) cards for each major entity.
- Draw Context Diagram, Data Flow Diagram (Level 0 and Level 1), and Use Case Diagram.
- Design responsive wireframes/prototypes for the main site and admin panel (desktop, tablet, mobile).

### 1.4.4 Front-End Development
- Build all HTML pages using semantic HTML5 and Bootstrap 5 for responsiveness.
- Apply custom CSS for branding (iTradeZA green/gold theme).
- Implement JavaScript (with jQuery) for form validation, dynamic cart updates, image previews, and UI enhancements.

### 1.4.5 Back-End Development
- Develop PHP scripts for: user authentication (registration, login, logout), product CRUD, cart management, checkout/order processing, buyer–seller messaging, and profile management.
- Develop admin panel with RBAC: CRUD for users, products, orders, and roles.
- Implement security measures: password hashing (bcrypt), prepared statements (PDO), CSRF tokens, input sanitisation.

### 1.4.6 Testing
- Test all user flows: registration → listing → browsing → cart → checkout → order tracking → messaging → reviews.
- Test admin flows: CRUD on users, products, orders, and roles.
- Cross-browser testing (Chrome, Firefox, mobile browsers).
- Responsive testing on phone, tablet, and desktop breakpoints.

### 1.4.7 Deployment & Documentation
- Host on InfinityFree or 000webhost; configure MySQL and upload files.
- Write comprehensive User Manual (Deliverable 3).
- Prepare presentation materials.

---

## 1.5 Timetable

| Description of Work | Start Date | End Date | Deliverable |
|---|---|---|---|
| Research, requirement analysis, and proposal writing | Block 1, Week 1 | Block 1, Week 4 | **Deliverable 1** – Project Proposal |
| Database design, system diagrams, wireframes/prototypes | Block 2, Week 1 | Block 2, Week 2 | Part of Deliverable 2 |
| Front-end development (HTML/CSS/JS for main site + admin) | Block 2, Week 2 | Block 2, Week 3 | Part of Deliverable 2 |
| Back-end development (PHP + MySQL integration) | Block 2, Week 2 | Block 2, Week 4 | Part of Deliverable 2 |
| Testing, bug fixes, and deployment to live server | Block 2, Week 4 | Block 2, Week 5 | **Deliverable 2** – Prototype + Code |
| User Manual writing and presentation preparation | Block 2, Week 5 | Block 2, Week 6 | **Deliverable 3** – Presentation |

### Gantt Chart

```
Block 1                          Block 2
W1    W2    W3    W4    W1    W2    W3    W4    W5    W6
|─────|─────|─────|─────|─────|─────|─────|─────|─────|─────|

[======== Research & Proposal =========]
                          D1 ▲
                                [=== Design & Diagrams ==]
                                      [==== Front-End Development ====]
                                      [======== Back-End Development =========]
                                                        [= Testing & Deploy =]
                                                                    D2 ▲
                                                              [== Manual & Pres ==]
                                                                          D3 ▲

Legend: ▲ = Submission deadline
        D1 = Deliverable 1 (Proposal)
        D2 = Deliverable 2 (Prototype + Code)
        D3 = Deliverable 3 (Presentation + User Manual)
```

---

## 1.6 Conclusion

The iTradeZA platform addresses a clear and pressing need within the South African digital economy. By creating a purpose-built C2C marketplace that is mobile-responsive, locally integrated, and trust-oriented, the project has the potential to:

- **Empower informal traders** to transition from "survival to scale" by giving them a free digital storefront.
- **Build digital trust** through seller verification and a transparent review system, combating the digital fraud that deters many township consumers.
- **Keep economic value local** by offering a platform developed for South Africa, supporting ZAR transactions and domestic payment methods, and reducing reliance on international platforms that extract fees from the local economy.
- **Promote financial inclusion** by supporting multiple payment methods (including cash on delivery and e-Wallet) that accommodate the under-banked population.

The technical stack—HTML, CSS, JavaScript, PHP, and MySQL with Bootstrap for responsiveness—ensures the platform can be developed within the project timeframe and hosted affordably on free-tier services. The role-based admin panel provides the governance infrastructure needed for a scalable and secure marketplace.

If successfully implemented, iTradeZA could serve as a model for how locally developed digital platforms can formalize the informal economy, foster entrepreneurship, and contribute meaningfully to South Africa's economic growth.

---

## Reference List

- Global Media Journal (2023). *Factors Influencing the Online Clothing Shopping Intention of Emerging Township Consumers in South Africa: The Mediation Effect of Attitude.* Available at: https://www.globalmediajournal.com/open-access/factors-influencing-the-online-clothing-shopping-intention-of-emerging-township-consumers-in-south-africa-the-mediation-effect-of-.php?aid=92394
- Standard Bank (2025). *Township Informal Economy Report (October 2025).* Available at: https://www.standardbank.co.za/static_file/South%20Africa/PDF/Township/Standard_Bank_Township_Informal_Economy_Report_October_2025.pdf
- Statistics South Africa (2025). *Quarterly Labour Force Survey (QLFS) – Q1: 2025.* Available at: https://www.statssa.gov.za/?page_id=1854&PPN=P0211
- World Wide Worx & Mastercard (2025). *South Africa's Online Retail Set to Surpass R130 Billion in 2025.* Available at: https://www.mastercard.com/news/eemea/en/newsroom/press-releases/en/2025-1/september/south-africa-s-online-retail-set-to-surpass-r130-billion-in-2025/
