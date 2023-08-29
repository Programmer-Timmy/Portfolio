# Portfolio System

This repository contains the source code for a portfolio system, designed to showcase your projects and provide
administrative functionality. The portfolio system allows users to browse projects, view project details, contact you,
and login as an admin to manage projects, add project details, and manage other users.

## Features

The portfolio system includes the following features:

### General Features

- **Home Page**: The home page provides an introduction to your portfolio and highlights featured projects.
- **About Page**: The about page provides information about you, your skills, and your experience.
- **Contact Page**: The contact page allows visitors to send you messages or inquiries.
- **Project Page**: The project page displays a list of projects, each with a project image, project details, and a link
  to the GitHub repository.

### Admin Features

- **Admin Login**: Admin users can log in using their credentials to access the admin dashboard.
- **Admin Dashboard**: The admin dashboard provides an interface for managing projects and users.
- **Project Management**: Admin users can add new projects, update project details, and delete existing projects.
- **Image Upload**: Admin users can upload project images to accompany project details.
- **GitHub Integration**: Admin users can add GitHub links to each project for easy access to the project repository.
- **User Management**: Admin users can manage other user accounts, including adding, updating, and deleting user
  profiles.

## Installation

To run the portfolio system on your local machine, follow these steps:

1. Clone the repository:

```bash
   git clone https://github.com/your-username/csharp-small-assignments.git
```

2. Set up a local PHP development environment such as XAMPP, WAMP, or MAMP.

3. Place the cloned repository in the appropriate directory of your local server (e.g., `htdocs` for XAMPP).

4. Start your local server and make sure it is running.

5. Import the provided SQL file (`database.sql`) into your database management system (e.g., phpMyAdmin) to set up the
   necessary database schema.

6. Update the database connection settings in the PHP files to match your local database configuration.

7. Open a web browser and navigate to `http://localhost/portfolio-system` to access the portfolio system.

## Usage

Once you have set up the portfolio system, you can perform the following actions:

### For Visitors

- Browse the home page to get an overview of your portfolio.
- Navigate to the about page to learn more about you and your skills.
- Visit the contact page to send you messages or inquiries.
- Explore the project page to view a list of projects, project details, and GitHub repository links.

### For Admin Users

- Log in to the admin dashboard using your admin credentials.
- Add new projects by providing project details, uploading an image file, and adding a GitHub link.
- Update project details, including the project image and GitHub link.
- Delete existing projects if needed.
- Manage other user accounts by adding, updating, or deleting user profiles.

