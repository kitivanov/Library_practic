import config from '../config.js';

import mongoose from "mongoose";

const mongoUrl = `mongodb://${config.mongo.host}:${config.mongo.port}/${config.mongo.db}`;

const digitalResourceSchema = new mongoose.Schema({
  title: { type: String, required: true },
  author: { type: String, required: true },
  format: { type: String, enum: ["pdf", "epub", "mp3"], required: true },
  fileSize: { type: Number, required: true },
  tags: { type: [String], default: [] },
  downloadCount: { type: Number, default: 0 }
});

const downloadLogSchema = new mongoose.Schema({
  resourceId: { type: mongoose.Schema.Types.ObjectId, ref: "DigitalResource", required: true },
  userId: { type: String, required: true },
  timestamp: { type: Date, default: Date.now }
});

const DigitalResource = mongoose.model("DigitalResource", digitalResourceSchema);
const DownloadLog = mongoose.model("DownloadLog", downloadLogSchema);

const resources = [
  { title: "Страх и ненависть в Лас-Вегасе", author: "Хантер С. Томпсон", format: "pdf", fileSize: 1024, tags: ["классика", "американская литература"] },
  { title: "1984", author: "Джордж Оруэлл", format: "epub", fileSize: 512, tags: ["антиутопия", "классика"] },
  { title: "Автостопом по галактике", author: "Дуглас Адамс", format: "pdf", fileSize: 768, tags: ["комедия", "научная фантастика"] },
  { title: "Мёртвые души", author: "Николай Гоголь", format: "epub", fileSize: 600, tags: ["классика", "русская литература"] }
];

async function init() {
  await mongoose.connect(mongoUrl);
  console.log("MongoDB connected");

  await DigitalResource.deleteMany({});
  await DownloadLog.deleteMany({});

  const insertedResources = await DigitalResource.insertMany(resources);
  console.log(`Inserted ${insertedResources.length} digital resources.`);

  await DownloadLog.create({
    resourceId: insertedResources[0]._id,
    userId: "123"
  });
  console.log("Created initial download log for first resource.");

  await mongoose.disconnect();
  console.log("Initialization complete.");
}

init().catch(err => {
  console.error("Error during initialization:", err);
  mongoose.disconnect();
});
