import VueRouter from 'vue-router';
import Header from "./components/Header";
import Home from "./components/home/Home";
import HomeDefault from "./components/home/HomeDefault";
import HomeGenre from "./components/home/HomeGenre";
import HomeAAA from "./components/home/HomeAAA";

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
import GuchiAAA from "./components/guchi/GuchiAAA";

import Hot from "./components/hot/Hot";


// import { component } from 'vue/types/umd';


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
            path: '/hot',
            name: 'hot',
            component: Hot,
        },
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
                    path: 'jobs',
                    name: 'home.genre',
                    component: HomeGenre,
                },
                {
                    // 仮ページ
                    path: 'aaa',
                    name: 'home.aaa',
                    component: HomeAAA,
                },
            ]
        },
        {
            path: '/user',
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
                    path: 'jobs',
                    name: 'guchi.genre',
                    component: GuchiGenre,
                },
                {
                    path: 'jobs/trend',
                    name: 'guchi.genre.trend',
                    component: GuchiGenreTrend,
                },
                {
                    path: '1',
                    name: 'guchi.detail',
                    component: GuchiDetail,
                },
                {
                    // 仮ページ
                    path: 'aaa',
                    name: 'guchi.aaa',
                    component: GuchiAAA,
                },
            ]
        },
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
