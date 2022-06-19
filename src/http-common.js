import axios from "axios";

//  baseURL: "http://localhost:8080/",
export default axios.create({
  headers: {
    "Content-type": "application/json"
  }
});
