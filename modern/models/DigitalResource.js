import mongoose from "mongoose";

const digitalResourceSchema = new mongoose.Schema({
  title: { type: String, required: true },
  author: { type: String, required: true },
  format: { type: String, enum: ["pdf", "epub", "mp3"], required: true },
  fileSize: { type: Number, required: true },
  tags: [String],
  downloadCount: { type: Number, default: 0 }
});

const DigitalResource = mongoose.model("DigitalResource", digitalResourceSchema);
export default DigitalResource;
