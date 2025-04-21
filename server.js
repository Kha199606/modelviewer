const express = require("express");
const cors = require("cors");
const bodyParser = require("body-parser");
const mysql = require("mysql2");

const app = express();
app.use(cors());
app.use(bodyParser.json());

const db = mysql.createConnection({
  host: "sql12.freesqldatabase.com",
  user: "sql12773959",
  password: "JIc9TxkfBV",
  database: "sql12773959",
  port: 3306,
});

db.connect(err => {
  if (err) {
    console.error("Database connection failed:", err);
  } else {
    console.log("Connected to MySQL database.");
  }
});

// API đăng ký
app.post("/api/auth/signup", (req, res) => {
  const { username, email, password } = req.body;
  db.query(
    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)",
    [username, email, password],
    (err, results) => {
      if (err) return res.json({ success: false, message: err.message });
      res.json({ success: true });
    }
  );
});

// API đăng nhập
app.post("/api/auth/login", (req, res) => {
  const { username, password } = req.body;
  db.query(
    "SELECT * FROM users WHERE email = ? AND password = ?",
    [username, password],
    (err, results) => {
      if (err || results.length === 0) {
        return res.json({ success: false });
      }
      res.json({ success: true, userId: results[0].id });
    }
  );
});

app.listen(3000, () => {
  console.log("Server is running on http://localhost:3000");
});
