<script>
  import axios from 'axios'
  //import md5 from 'md5';
  import StreamMD5 from 'stream-md5';

  export default {
    watch: {
      chunks(n, o) {
        if (n.length > 0) {
          if (n || o) {
            console.log(this.chunks.length+":"+this.md5sum);
          }
          var useMD5=false;
          if (useMD5) {
            var br=new FileReader();
            br.addEventListener('loadend', event=>{
              var data=new Uint8Array(event.target.result);
              var state=StreamMD5.init();
              StreamMD5.update(state, data);
              this.md5sum=StreamMD5.finalize(state);
              console.log(this.md5sum);
              console.log("loadend "+n.length);
            });
            console.log("Before "+n.length);
            br.readAsArrayBuffer(n[0]);
            console.log("After "+n.length);
          }

          this.upload();
        }
      }
    },

    data() {
      return {
        file: null,
        chunks: [],
        chunkSize: 100000000,
        totalChunks: 0,
        uploaded: 0,
        md5sum: "",
        err: "" 
      };
    },

    computed: {
      progress() {
		if (this.file==null) return 0;
        //console.log(this.uploaded + "/" + this.file.size);
        //console.log(Math.floor((this.uploaded * 100) / this.file.size));
        //return Math.floor((this.uploaded * 100) / this.file.size);
        //return this.uploaded;
        return 100-Math.floor((this.chunks.length * 100) / this.totalChunks);
      },
      formData() {
        let formData = new FormData;
        // console.log(this.file);
        formData.set('is_last', this.chunks.length === 1);
        formData.set('file', this.chunks[0], `${this.file.name}.part`);
        // console.log(formData.get('file'));
        return formData;
      },
      config() {
        return {
          method: 'POST',
          data: this.formData,
          url: 'upload/?file='+this.file.name+
            "&chunk="+(this.totalChunks-this.chunks.length)+
            "&md5="+this.md5sum+
            "&limit="+this.totalChunks,
          headers: {
            'Content-Type': 'application/octet-stream'
          },
          onUploadProgress: event => {
            //this.uploaded += event.loaded;
            const { loaded, total } = event;
            this.uploaded=Math.floor((loaded*100)/total);
          }
          //transformResponse: data => {
          //  console.log(data);
          //  return data;
          //}
        };
      }
    },

    methods: {
      select(event) {
        this.file = event.target.files.item(0);
        this.createChunks();
      },
      upload() {
        console.log("Uploading");
        const fileExists=async()=>{
          try {
            var url='upload/?file='+this.file.name+
              "&chunk="+(this.totalChunks-this.chunks.length)+
              "&limit="+this.totalChunks;
            const resp = await axios.get(url);
            console.log(resp);
            if (resp.data.exists && resp.data.filesize == this.chunks[0].length) {
              console.log("Skipping/shift file "+this.file.name);
              this.chunks.shift();
            } else {
              console.log("Uploading file "+this.file.name);
              this.uploadFile();
            }
          } catch (err) {
            console.error(err);
          }
        };
        fileExists();
        console.log("await 96?"+"&chunk="+(this.totalChunks-this.chunks.length));
      },
      uploadFile() {
        axios(this.config).then(response => {
          //console.log(response);
          if (response.data.return) {
            this.chunks.shift();
          } else {
            this.err="Upload error: "+response.message;
          }
        }).catch(error => {console.log(error)});
      },
      createChunks() {
        let size = this.chunkSize, chunks = Math.ceil(this.file.size / size);
        this.totalChunks=chunks;
	// console.log(this.file.type);

        for (let i = 0; i < chunks; i++) {
          this.chunks.push(this.file.slice(
            i * size, Math.min(i * size + size, this.file.size), this.file.type
          ));
          //var br=new FileReader();
          //br.addEventListener('loadend', event=>{
          //  console.log(md5(new TextDecoder().decode(event.target.result)));
          //});
          //br.readAsArrayBuffer(this.chunks[this.chunks.length-1]);
        }
      }
    }
  }
</script>

<template>
  <div>
    <input type="file" @change="select">
    <progress :value="progress" :max="100"></progress>
    <div v-if="chunks.length==0 && totalChunks>0">done</div>
    <div v-else>{{ md5sum }}</div>
    <p class="error-message" v-show="err.length > 0">
      {{ err }}
    </p>
  </div>
</template>
