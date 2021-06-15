/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import VueAxios from 'vue-axios';
import PolarisVue from '@hulkapps/polaris-vue';
import '@hulkapps/polaris-vue/dist/polaris-vue.css';
import { router } from './router';
import axios from 'axios';
import App from './App.vue';

Vue.use(PolarisVue);
Vue.use(VueAxios, axios);

const app = new Vue({
    el: '#app',
    router,
    render: h => h(App)
});
