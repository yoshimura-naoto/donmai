<template>

  <div class="trend-page">

    <div class="trend">

      <div class="trend-container">

        <div class="trend-title">
          トレンドのタグ（1週間）
        </div>

        <div class="trend-box">
          
          <router-link :to="{ name: 'tags.new', params: { name: trend.name.replace('#', '') }}" v-for="(trend, index) in trends" :key="index" class="trend-each">
  
            <div class="trend-rank">
              {{ index + 1 }}位
            </div>
  
            <div class="trend-tag">
              #{{ trend.name }}
            </div>
  
            <div class="trend-bar-area">

              <div class="trend-bar"></div>

            </div>

            <div class="trend-post-count">
              {{ trend.postCountShow }}件
            </div>
  
          </router-link>

        </div>

      </div>

    </div>

  </div>
  
</template>


<script>
export default {
  data() {
    return {
      frame: 0,
      intervalId: 0,
      trends: [],
    }
  },

  methods: {
    // タグ数のランキング情報の取得
    getTagRank() {
      axios.get('/api/tag/trend')
        .then((res) => {
          // console.log(res.data);
          if (res.data.length > 0) {
            this.trends = res.data;
            this.$nextTick(function () {
              this.makeGraph();
              this.postCountUp();
            });
          }
        }).catch(() => {
          return;
        });
    },
    // グラフの描画
    makeGraph() {
      const firstPostCount = this.trends[0].posts_count;
      for (let i = 0; i < this.trends.length; i++) {
        const trend = document.querySelector('.trend-box').children[i];
        const postsCount = this.trends[i].posts_count;
        trend.children[2].style.width = postsCount / firstPostCount * 100 + '%';
      }
    },
    postCountPlus() {
      const frameCount = 50;
      this.frame++;
      for (let i = 0; i < this.trends.length; i++) {
        this.trends[i].postCountNow += this.trends[i].posts_count / frameCount;
        this.trends[i].postCountShow = Math.floor(this.trends[i].postCountNow);
        if (this.frame === frameCount) {
          this.trends[i].postCountShow = this.trends[i].posts_count;
        }
      }
      if (this.frame == frameCount) {
        clearInterval(this.intervalId);
      }
    },
    postCountUp() {
      let self = this;
      this.intervalId = setInterval(function() {self.postCountPlus()}, 20);
    }
  },

  computed: {

  },

  mounted() {
    this.getTagRank();
    // this.makeGraph();
    // this.postCountUp();
  },
}
</script>