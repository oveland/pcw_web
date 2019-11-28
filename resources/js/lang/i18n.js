import Vue from 'vue';
import VueI18n from "vue-i18n";

// WARNING: file es.json is a symbolic link to PATH_TO_LARAVEL)PROJECT/resources/lang/es.json
import ES from './es';

Vue.use(VueI18n);

const messages = {
    es: ES
};

const i18n = new VueI18n({
    messages,
    locale: 'es'
});

export default i18n;