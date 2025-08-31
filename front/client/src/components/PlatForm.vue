<template>
  <div class="card">
    <h3>{{ edited?.id ? "Modifier un plat" : "Ajouter un plat" }}</h3>

    <form @submit.prevent="submit">
      <div class="grid">
        <label>
          <span>Nom du plat</span>
          <input v-model="form.name" placeholder="Nom du plat" required />
        </label>

        <label>
          <span>Prix</span>
          <input v-model.number="form.price" type="number" step="0.01" placeholder="Prix" required />
        </label>
      </div>

      <div class="actions">
        <button class="btn" type="submit">{{ edited?.id ? "Enregistrer" : "Ajouter" }}</button>
        <button class="btn ghost" type="button" @click="reset">Annuler</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, watch } from "vue";
import { usePlatStore } from "@/stores/platStore";

const props = defineProps({ edited: Object });
const emit = defineEmits(["saved", "cancel"]);
const store = usePlatStore();

const form = reactive({ name: "", price: null });

watch(
  () => props.edited,
  (v) => {
    if (v?.id) {
      form.name = v.name || v.nom || "";
      form.price = v.price ?? v.prix ?? null;
    } else {
      reset();
    }
  },
  { immediate: true }
);

async function submit() {
  const payload = { name: form.name, price: form.price };
  if (props.edited?.id) {
    await store.update(props.edited.id, payload);
  } else {
    await store.create(payload);
  }
  emit("saved");
  reset();
}

function reset() {
  form.name = "";
  form.price = null;
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
