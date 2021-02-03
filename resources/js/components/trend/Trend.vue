<template>

  <div class="trend-page">

    <div class="trend">

      <div class="trend-container">

        <div class="trend-title">
          トレンド
        </div>

        <div class="trend-box">
          
          <router-link :to="{ name: 'tags.new', params: { name: trend.tag.replace('#', '') }}" v-for="(trend, index) in trends" :key="index" class="trend-each">
  
            <div class="trend-rank">
              {{ index + 1 }}位
            </div>
  
            <div class="trend-tag">
              {{ trend.tag }}
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
      trends: [
        {
          tag: '#うんち',
          postCount: 124324,
          postCountShow: 0,
        },
        {
          tag: '#おやしろさま',
          postCount: 91563,
          postCountShow: 0,
        },
        {
          tag: '#ばか',
          postCount: 82123,
          postCountShow: 0,
        },
        {
          tag: '#シンジ',
          postCount: 70543,
          postCountShow: 0,
        },
        {
          tag: '#エヴァンゲリオン',
          postCount: 63056,
          postCountShow: 0,
        },
        {
          tag: '#ひぐらし',
          postCount: 50375,
          postCountShow: 0,
        },
        {
          tag: '#さとこ',
          postCount: 30274,
          postCountShow: 0,
        },
        {
          tag: '#ハゲ',
          postCount: 22647,
          postCountShow: 0,
        },
        {
          tag: '#うへへ',
          postCount: 3000,
          postCountShow: 0,
        },
      ]
    }
  },
  methods: {
    // グラフの描画
    makeGraph() {
      const firstPostCount = this.trends[0].postCount;
      for (let i = 0; i < this.trends.length; i++) {
        const trend = document.querySelector('.trend-box').children[i];
        const postCount = this.trends[i].postCount;
        trend.children[2].style.width = postCount / firstPostCount * 100 + '%';
      }
    },
    postCountPlus() {
      const frameCount = 50;
      this.frame++;
      for (let i = 0; i < this.trends.length; i++) {
        this.trends[i].postCountShow += Math.floor(this.trends[i].postCount / frameCount);
        if (this.frame == frameCount) {
          this.trends[i].postCountShow = this.trends[i].postCount;
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
  mounted() {
    this.makeGraph();
    this.postCountUp();
  },
}
</script>