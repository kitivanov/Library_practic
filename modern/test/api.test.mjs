import { expect } from "chai";
import axios from "axios";

describe("Library API Tests", function() {
  this.timeout(5000);

  const baseUrl = "http://localhost:3000";
  let digitalResourceId;

  // Получаем существующий resourceId перед тестом скачивания
  before(async () => {
    try {
      const res = await axios.get(`${baseUrl}/api/digital/resources`);
      if (res.data.length > 0) {
        digitalResourceId = res.data[0]._id;
      } else {
        console.warn("No digital resources found for download test");
      }
    } catch (err) {
      console.error("Error fetching digital resources:", err.response?.data || err.message);
    }
  });

  it("GET /api/physical/books should return array", async () => {
    try {
      const res = await axios.get(`${baseUrl}/api/physical/books`);
      console.log("GET /api/physical/books response:", res.data);
      expect(res.status).to.equal(200);
      expect(res.data).to.be.an("array");
    } catch (err) {
      console.error("Error:", err.response?.data || err.message);
      throw err;
    }
  });

  it("POST /api/physical/loan should register a loan", async () => {
    try {
      const res = await axios.post(`${baseUrl}/api/physical/loan`, {
        inventory_number: "LIB-2024-001",
        reader_card: "CARD-001"
      });
      console.log("POST /api/physical/loan response:", res.data);
      expect(res.status).to.equal(200);
      expect(res.data).to.have.property("message");
    } catch (err) {
      console.error("Error:", err.response?.data || err.message);
      throw err;
    }
  });

  it("GET /api/digital/resources should return resources array", async () => {
    try {
      const res = await axios.get(`${baseUrl}/api/digital/resources`);
      console.log("GET /api/digital/resources response:", res.data);
      expect(res.status).to.equal(200);
      expect(res.data).to.be.an("array");
    } catch (err) {
      console.error("Error:", err.response?.data || err.message);
      throw err;
    }
  });

  it("POST /api/digital/download should log download", async () => {
    if (!digitalResourceId) {
      this.skip();
    }
    try {
      const res = await axios.post(`${baseUrl}/api/digital/download`, {
        resourceId: digitalResourceId,
        userId: "USER-001"
      });
      console.log("POST /api/digital/download response:", res.data);
      expect(res.status).to.equal(200);
      expect(res.data).to.have.property("fileUrl");
    } catch (err) {
      console.error("Download error:", err.response?.data || err.message);
      throw err;
    }
  });
});
