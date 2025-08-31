import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import { createI18n } from "vue-i18n";
import messagesFr from "./i18n/messages_fr.json";
import messagesEn from "./i18n/messages_en.json";
import "./assets/style.css";


const i18n = createI18n({
  locale: "fr",
  fallbackLocale: "en",
  messages: {
    fr: messagesFr,
    en: messagesEn
  }
});

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(i18n);

app.mount("#app");
