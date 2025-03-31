# 📚 BookSwap - Community Book Exchange Platform

![BookSwap Screenshot](assets/images/screenshot.png) *Example: BookSwap Dashboard*

A full-stack web application that connects book lovers to exchange books with nearby users. Built with PHP, MySQL, and modern frontend technologies.

## ✨ Key Features

- **Location-Based Matching**: Find books available near you
- **Book Management**: Add/edit books with images and details
- **Smart Filters**: Search by genre, condition, and distance
- **Messaging System**: Built-in chat for exchange arrangements
- **User Profiles**: Manage your book collection and preferences
- **Exchange Tracking**: Monitor request status (Pending/Accepted/Completed)

## 🛠️ Technology Stack

| Component       | Technology |
|-----------------|------------|
| Frontend        | HTML5, CSS3, JavaScript |
| Backend         | PHP 7.4+   |
| Database        | MySQL 5.7+ |
| Server          | Apache (XAMPP) |
| UI Framework    | Custom CSS with Flexbox/Grid |
| Icons           | Font Awesome 6 |
| Fonts           | Google Fonts (Inter) |

## 📂 Project Structure

```bash
bookswap/
├── assets/            # Static assets
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── images/        # Image assets
│
├── includes/          # Core PHP components
│   ├── config.php     # Configuration
│   ├── db.php         # Database connection
│   ├── functions.php  # Helper functions
│   ├── header.php     # Header template
│   └── footer.php     # Footer template
│
├── pages/             # Main application pages
│   ├── dashboard.php  # User dashboard
│   ├── browse.php     # Book browsing
│   ├── add_book.php   # Add/edit books
│   ├── profile.php    # User profile
│   └── messages.php   # Messaging system
│
├── auth/              # Authentication
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── index.php          # Homepage
└── README.md          # This file

## 🖥️ Usage Instructions

- **For Readers:**
Register an account
Browse available books
Request books from other users
Arrange exchanges via messaging

- **For Book Owners:**
Add books to your collection
Manage incoming requests
Mark exchanges as completed

##🤝 Contributing

We welcome contributions! Please follow these steps:
Fork the project
Create your feature branch (git checkout -b feature/YourFeature)
Commit your changes (git commit -m 'Add some feature')
Push to the branch (git push origin feature/YourFeature)
Open a Pull Request

## 📜 License
Distributed under the MIT License. See LICENSE for more information.

## 📬 Contact
Project Maintainer: Addul Moeid Rao
Project Link: https://github.com/yourusername/bookswap
