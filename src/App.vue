<template>
  <div id="app">
    <div class="container" style="width:600px">
      <div style="margin: 20px">
        <h4>Upload Files</h4>
      </div>

      <Uploader></Uploader>
    </div>
  </div>
</template>

<script>
import Uploader from "./components/Uploader";

export default {
  name: "App",
  components: {
    Uploader
  }
};
</script>
