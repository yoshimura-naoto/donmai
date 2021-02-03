import VueRouter from 'vue-router';
import Header from "./components/Header";
import Home from "./components/home/Home";
import HomeDefault from "./components/home/HomeDefault";
import HomeGenre from "./components/home/HomeGenre";

import User from "./components/user/User";
import UserPosts from "./components/user/UserPosts";
import UserDonmai from "./components/user/UserDonmai";
import UserGuchi from "./components/user/UserGuchi";

import Guchi from "./components/guchi/Guchi";
import GuchiAll from "./components/guchi/GuchiAll";
import GuchiAllTrend from "./components/guchi/GuchiAllTrend";
import GuchiGenre from "./components/guchi/GuchiGenre";
import GuchiGenreTrend from "./components/guchi/GuchiGenreTrend";
import GuchiDetail from "./components/guchi/GuchiDetail";

import Hot from "./components/hot/Hot";

import Tags from "./components/tags/Tags";
import TagsNew from "./components/tags/TagsNew";
import TagsPopular from "./components/tags/TagsPopular";

import Trend from "./components/trend/Trend";

import Search from "./components/search/Search";
import SearchPostNew from "./components/search/SearchPostNew";
import SearchPostPopular from "./components/search/SearchPostPopular";
import SearchGuchiNew from "./components/search/SearchGuchiNew";
import SearchGuchiPopular from "./components/search/SearchGuchiPopular";
import SearchUser from "./components/search/SearchUser";


/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    // 画面遷移時のスクロール位置の制御
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    },
    routes: [
        {
            path: '/',
            component: Home,
            children: [
                {
                    path: '',
                    name: 'home',
                    component: HomeDefault,
                },
                {
                    path: 'genre/:name',
                    name: 'home.genre',
                    component: HomeGenre,
                },
            ]
        },
        {
            path: '/user/:id',
            component: User,
            children: [
                {
                    path: '',
                    name: 'user',
                    component: UserPosts,
                },
                {
                    path: 'donmai',
                    name: 'user.donmai',
                    component: UserDonmai,
                },
                {
                    path: 'guchi',
                    name: 'user.guchi',
                    component: UserGuchi,
                },
            ]
        },
        {
            path: '/guchi',
            component: Guchi,
            children: [
                {
                    path: 'all',
                    name: 'guchi.all',
                    component: GuchiAll,
                },
                {
                    path: 'all/trend',
                    name: 'guchi.all.trend',
                    component: GuchiAllTrend,
                },
                {
                    path: 'room/1',
                    name: 'guchi.detail',
                    component: GuchiDetail,
                },
                {
                    path: ':name',
                    name: 'guchi.genre',
                    component: GuchiGenre,
                },
                {
                    path: ':name/trend',
                    name: 'guchi.genre.trend',
                    component: GuchiGenreTrend,
                },
            ]
        },
        {
            path: '/hot',
            name: 'hot',
            component: Hot,
        },
        {
            path: '/tags/:name',
            component: Tags,
            children: [
                {
                    path: '',
                    name: 'tags.new',
                    component: TagsNew,
                },
                {
                    path: 'popular',
                    name: 'tags.popular',
                    component: TagsPopular,
                }
            ],
        },
        {
            path: '/trend',
            name: 'trend',
            component: Trend,
        },
        {
            path: '/search/:word',
            component: Search,
            children: [
                {
                    path: 'post/new',
                    name: 'search.post.new',
                    component: SearchPostNew,
                },
                {
                    path: 'post/popular',
                    name: 'search.post.popular',
                    component: SearchPostPopular,
                },
                {
                    path: 'guchi/new',
                    name: 'search.guchi.new',
                    component: SearchGuchiNew
                },
                {
                    path: 'guchi/popular',
                    name: 'search.guchi.popular',
                    component: SearchGuchiPopular,
                },
                {
                    path: 'user/',
                    name: 'search.user',
                    component: SearchUser,
                }
            ]
        }
    ]
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('header-component', Header);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router
});
