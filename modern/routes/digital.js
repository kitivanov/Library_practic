import express from "express";
import DigitalResource from "../models/DigitalResource.js";
import DownloadLog from "../models/DownloadLog.js";

const router = express.Router();

router.get("/resources", async (req, res) => {
  try {
    const resources = await DigitalResource.find({});
    res.json(resources);
  } catch (err) {
    console.error("Resources error:", err);
    res.status(500).json({ error: "Internal server error" });
  }
});

router.post("/download", async (req, res) => {
  try {
    const { resourceId, userId } = req.body;
    if (!resourceId || !userId) return res.status(400).json({ error: "Missing parameters" });

    const resource = await DigitalResource.findById(resourceId);
    if (!resource) return res.status(404).json({ error: "Resource not found" });

    resource.downloadCount += 1;
    await resource.save();

    await DownloadLog.create({ resourceId, userId });

    res.json({ fileUrl: `/downloads/${resource._id}` });
  } catch (err) {
    console.error("Download error:", err);
    res.status(500).json({ error: "Internal server error" });
  }
});

export default router;
