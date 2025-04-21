const express = require("express");
const mysql = require("mysql2");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());

// Cấu hình MySQL
const db = mysql.createConnection({
  host: "sql203.infinityfree.com",
  user: "if0_38658183",
  password: "1wzsyzgrpckz",
  database: "if0_38658183_modelviewer",
  port: 3306
});

// Tạo bảng nếu chưa có
db.query(`
  CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(100)
  )
`);

// Signup
app.post("/signup", (req, res) => {
  const { username, email, password } = req.body;
  db.query("INSERT INTO users (username, email, password) VALUES (?, ?, ?)", [username, email, password], (err) => {
    if (err) return res.status(400).send("Email already exists or error occurred.");
    res.send("Signup successful!");
  });
});

// Login
app.post("/login", (req, res) => {
  const { email, password } = req.body;
  db.query("SELECT * FROM users WHERE email = ? AND password = ?", [email, password], (err, results) => {
    if (err) return res.status(500).send("Server error.");
    if (results.length > 0) res.send("Login successful!");
    else res.status(401).send("Invalid credentials.");
  });
});

// Khởi chạy server
app.listen(3000, () => console.log("Server running on http://localhost:3000"));
