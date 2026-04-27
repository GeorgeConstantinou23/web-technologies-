# CO1418 Assignment 2 Storefront

## Student Details
- Name: Georgios Constantinou
- Student ID: [21304276]
- Homepage URL: https://vesta.uclan.ac.uk/~gconstantinou/WebTec/index.php
- Dummy account email: [dummy@gmail.com]
- Dummy account password: [Dummy123!]
- GitHub repository: https://github.com/GeorgeConstantinou23/web-technologies-

## Project Overview
This project is a server-side PHP and MySQL storefront for the UClan Web Page assignment. The site displays products and offers from the database, allows users to register and log in, supports product reviews, stores cart items in a browser cookie, and creates order records during checkout.

## Main Features
- Homepage with embedded media and current offers from `tbl_offers`
- Products page with product data from `tbl_products`
- Stock filter dropdown for all products, good stock, last few, and out of stock
- Item detail page for individual products
- Review display and review submission for logged-in users
- User registration and login using `tbl_users`
- Personalised welcome message for logged-in users
- Cookie-based shopping cart
- Offer code support on the cart page
- Checkout process that inserts orders into `tbl_orders`
- Custom `404.php` error page for missing pages inside the project folder
- Responsive navigation with a mobile hamburger menu

## Pages
- `index.php`: homepage with media content and database offers
- `products.php`: product catalogue with stock filtering and add-to-cart controls
- `item.php`: individual product page with reviews and review form
- `cart.php`: cart page with cart items, quantities, offer code input, and checkout button
- `checkout.php`: server-side checkout handler that validates the cart and creates orders
- `login.php`: login form and authentication flow
- `register.php`: registration form and user creation flow
- `logout.php`: clears the login session
- `404.php`: custom page not found screen

## Shared Code
- `includes/bootstrap.php`: starts sessions, loads shared files, and opens the database connection
- `includes/helpers.php`: shared helper functions such as escaping, flash messages, and stock class mapping
- `includes/offers.php`: loads offers and extracts offer codes and discount values
- `includes/products.php`: loads all products and single-product records
- `includes/users.php`: handles registration and login authentication
- `includes/reviews.php`: loads, averages, and saves product reviews
- `includes/orders.php`: calculates cart totals and creates order records
- `includes/header.php`: shared page header, navigation, and welcome message
- `includes/footer.php`: shared footer and JavaScript loading
- `cart.js`: browser-side cart storage, cart rendering, offer-code display, and mobile navigation
- `style.css`: site layout and visual styling

## Database Tables Used
- `tbl_products`: stores product title, price, stock status, image path, and description
- `tbl_offers`: stores homepage offer content
- `tbl_users`: stores user account details and hashed passwords
- `tbl_reviews`: stores product reviews, ratings, and review text
- `tbl_orders`: stores completed orders using user ID and product ID list

## Security And Validation
- User passwords are stored with `password_hash()`
- Login checks passwords with `password_verify()`
- User output is escaped with the helper function `e()`
- Registration validates required fields, email format, and password length
- Review submission validates title, comment, rating, user ID, and product ID
- Checkout recalculates product prices on the server instead of trusting cookie prices
- Offer codes are checked again on the server during checkout
- Out-of-stock products are blocked during server-side checkout

## Development Decisions
- The cart is stored in a cookie because the assignment required cookie usage
- Product prices are recalculated on the server to avoid trusting client-side cart data
- The site is split into shared include files to reduce repeated database and layout code
- The custom `404.php` page uses the same header and footer as the rest of the website
- `.htaccess` is used to route missing pages inside the `WebTec` folder to the custom 404 page

## Testing Checklist
- Homepage loads and shows offers from the database
- Product catalogue loads from `tbl_products`
- Product stock filter changes the displayed products
- Individual product pages open correctly
- Registration creates a new user account
- Login starts a session and shows the personalised welcome message
- Logged-in users can submit product reviews
- Cart stores selected products using cookies
- Offer code input updates the cart total
- Checkout creates a row in `tbl_orders`
- Out-of-stock products cannot be checked out
- Custom 404 page appears for missing pages inside `/WebTec/`
- Website has been tested on Vesta

## Supportive Resources
- PHP manual documentation for mysqli, sessions, password hashing, and form handling
- W3Schools and MDN references for HTML, CSS, JavaScript, cookies, and responsive layout
- UClan assignment material and database structure

## Video Demonstration
A 3-minute video demonstration is provided separately as required by the assignment brief.
