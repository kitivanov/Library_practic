import mongoose from "mongoose";

const downloadLogSchema = new mongoose.Schema({
  resourceId: { type: mongoose.Schema.Types.ObjectId, ref: "DigitalResource", required: true },
  userId: { type: String, required: true },
  timestamp: { type: Date, default: Date.now }
});

const DownloadLog = mongoose.model("DownloadLog", downloadLogSchema);
export default DownloadLog;
