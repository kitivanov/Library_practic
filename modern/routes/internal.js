import config from '../config.js';

import express from "express";
import { parseStringPromise } from "xml2js";

const router = express.Router();

router.get("/overdue-report", async (req, res) => {
  try {
    const response = await fetch(`http://${config.php.host}:${config.php.port}/report.php?type=overdue&format=xml`);
    const xmlText = await response.text();

    const parsed = await parseStringPromise(xmlText, { explicitArray: false });

    if (!parsed.overdueBooks || !parsed.overdueBooks.book) {
      parsed.overdueBooks = { book: [] };
    } else if (!Array.isArray(parsed.overdueBooks.book)) {
      parsed.overdueBooks.book = [parsed.overdueBooks.book];
    }

    res.json(parsed);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Failed to fetch or parse overdue report" });
  }
});

router.get('/admin-url', (req, res) => {
  const adminUrl = `http://${config.admin.host}:${config.admin.port}/admin.php?action=xml`;
  res.json({ adminUrl });
});

export default router;