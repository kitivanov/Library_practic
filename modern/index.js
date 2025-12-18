import config from './config.js';


import express from "express";
import mongoose from "mongoose";
import bodyParser from "body-parser";
import path from "path";
import { fileURLToPath } from "url";

import physicalRoutes from "./routes/physical.js";
import digitalRoutes from "./routes/digital.js";
import internalRoutes from "./routes/internal.js";


const app = express();

app.use(bodyParser.json());

const mongoUrl = `mongodb://${config.mongo.host}:${config.mongo.port}/${config.mongo.db}`;


mongoose.connect(mongoUrl)
  .then(() => console.log("MongoDB connected"))
  .catch(err => console.error("MongoDB connection error:", err));

app.use("/api/physical", physicalRoutes);
app.use("/api/digital", digitalRoutes);
app.use("/api/internal", internalRoutes);

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

app.use(express.static(path.join(__dirname, "..", "frontend")));

app.use((req, res) => {
  res.sendFile(path.join(__dirname, "..", "frontend", "index.html"));
});

app.listen(config.node.port, () => {
  console.log(`Server running on http://${config.node.host}:${config.node.port}`);
});
