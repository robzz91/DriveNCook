<template>
  <div class="card">
    <h3>{{ edited?.id ? "Modifier un client" : "Ajouter un client" }}</h3>

    <form @submit.prevent="submit">
      <div class="grid">
        <label>
          <span>Nom</span>
          <input v-model="form.name" placeholder="Nom" required />
        </label>

        <label>
          <span>Email</span>
          <input v-model="form.email" type="email" placeholder="Email" required />
        </label>
      </div>

      <div class="actions">
        <button class="btn" type="submit" :disabled="loading">
          {{ edited?.id ? "Enregistrer" : "Ajouter" }}
        </button>
        <button class="btn ghost" type="button" @click="reset">Annuler</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, watch, ref } from "vue";
import { useClientStore } from "@/stores/clientStore";

const props = defineProps({ edited: Object });
const emit = defineEmits(["saved", "cancel"]);
const store = useClientStore();
const loading = ref(false);

const form = reactive({ name: "", email: "" });

watch(
  () => props.edited,
  (v) => {
    if (v?.id) {
      form.name = v.name || v.nom || "";
      form.email = v.email || "";
    } else {
      reset();
    }
  },
  { immediate: true }
);

async function submit() {
  loading.value = true;
  try {
    if (props.edited?.id) {
      await store.update(props.edited.id, form);
    } else {
      await store.create(form);
    }
    emit("saved");
    reset();
  } finally {
    loading.value = false;
  }
}

function reset() {
  form.name = "";
  form.email = "";
  emit("cancel");
}
</script>

<style scoped>
.card { background:#1d1f23; border-radius:12px; padding:16px; }
.grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
label span { display:block; color:#8b949e; margin-bottom:6px; }
input { width:100%; padding:10px; border-radius:6px; border:1px solid #2a2e35; background:#151a21; color:#c9d1d9; }
.actions { display:flex; gap:10px; margin-top:12px; }
.btn { background:#f7b500; border:none; padding:8px 12px; border-radius:6px; color:#151a21; cursor:pointer; }
.btn.ghost { background:transparent; border:1px solid #2a2e35; color:#c9d1d9; }
</style>
