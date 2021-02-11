import VueRouter from 'vue-router';
import Header from "./components/Header";
import Home from "./components/home/Home";
import HomeDefault from "./components/home/HomeDefault";
import HomeGenre from "./components/home/HomeGenre";

import User from "./components/user/User";
import UserPosts from "./components/user/UserPosts";
import UserDonmai from "./components/user/UserDonmai";
import UserGuchi from "./components/user/UserGuchi";
import UserEdit from "./components/user/UserEdit";
import UserPassword from "./components/user/UserPassword";

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

import Register from "./components/auth/Register";
import Login from "./components/auth/Login";


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
    // 画面遷移時にスクロール位置をトップに
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
                    meta: { authOnly: true },
                },
                {
                    path: 'genre/:name',
                    name: 'home.genre',
                    component: HomeGenre,
                    meta: { authOnly: true },
                },
            ]
        },
        {
            path: '/user/edit',
            name: 'user.edit',
            component: UserEdit,
            meta: { authOnly: true },
        },
        {
            path: '/user/password',
            name: 'user.password',
            component: UserPassword,
            meta: { authOnly: true },
        },
        {
            path: '/user/:id',
            component: User,
            children: [
                {
                    path: '',
                    name: 'user',
                    component: UserPosts,
                    meta: { authOnly: true },
                },
                {
                    path: 'donmai',
                    name: 'user.donmai',
                    component: UserDonmai,
                    meta: { authOnly: true },
                },
                {
                    path: 'guchi',
                    name: 'user.guchi',
                    component: UserGuchi,
                    meta: { authOnly: true },
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
                    meta: { authOnly: true },
                },
                {
                    path: 'all/trend',
                    name: 'guchi.all.trend',
                    component: GuchiAllTrend,
                    meta: { authOnly: true },
                },
                {
                    path: 'room/:id',
                    name: 'guchi.detail',
                    component: GuchiDetail,
                    meta: { authOnly: true },
                },
                {
                    path: ':name',
                    name: 'guchi.genre',
                    component: GuchiGenre,
                    meta: { authOnly: true },
                },
                {
                    path: ':name/trend',
                    name: 'guchi.genre.trend',
                    component: GuchiGenreTrend,
                    meta: { authOnly: true },
                },
            ]
        },
        {
            path: '/hot',
            name: 'hot',
            component: Hot,
            meta: { authOnly: true },
        },
        {
            path: '/tags/:name',
            component: Tags,
            children: [
                {
                    path: '',
                    name: 'tags.new',
                    component: TagsNew,
                    meta: { authOnly: true },
                },
                {
                    path: 'popular',
                    name: 'tags.popular',
                    component: TagsPopular,
                    meta: { authOnly: true },
                }
            ],
        },
        {
            path: '/trend',
            name: 'trend',
            component: Trend,
            meta: { authOnly: true },
        },
        {
            path: '/search/:word',
            component: Search,
            children: [
                {
                    path: 'post/new',
                    name: 'search.post.new',
                    component: SearchPostNew,
                    meta: { authOnly: true },
                },
                {
                    path: 'post/popular',
                    name: 'search.post.popular',
                    component: SearchPostPopular,
                    meta: { authOnly: true },
                },
                {
                    path: 'guchi/new',
                    name: 'search.guchi.new',
                    component: SearchGuchiNew,
                    meta: { authOnly: true },
                },
                {
                    path: 'guchi/popular',
                    name: 'search.guchi.popular',
                    component: SearchGuchiPopular,
                    meta: { authOnly: true },
                },
                {
                    path: 'user/',
                    name: 'search.user',
                    component: SearchUser,
                    meta: { authOnly: true },
                }
            ]
        },
        {
            path: '/register',
            name: 'auth.register',
            component: Register,
            meta: { guestOnly: true },
        },
        {
            path: '/login',
            name: 'auth.login',
            component: Login,
            meta: { guestOnly: true },
        },
    ]
});


function isLoggedIn() {
    return localStorage.getItem('auth');
}

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.authOnly)) {
        if (!isLoggedIn()) {
            next({ name: 'auth.login' });
        } else {
            next();
        }
    } else if (to.matched.some(record => record.meta.guestOnly)) {
        if (isLoggedIn()) {
            next({ name: 'home' });
        } else {
            next();
        }
    } else {
        next();
    }
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
