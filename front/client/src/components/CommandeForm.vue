<template>
  <div class="card">
    <h3>{{ edited?.id ? "Modifier la commande" : "Passer une commande" }}</h3>

    <form @submit.prevent="submit">
      <div class="grid">
        <label>
          <span>Client</span>
          <select v-model="form.client_id" required>
            <option value="" disabled>— Sélectionner —</option>
            <option v-for="c in clients.list" :key="c.id" :value="c.id">
              {{ c.name || c.nom }} ({{ c.email }})
            </option>
          </select>
        </label>

        <label>
          <span>Plat</span>
          <select v-model="form.plat_id" required>
            <option value="" disabled>— Sélectionner —</option>
            <option v-for="p in plats.list" :key="p.id" :value="p.id">
              {{ p.name || p.nom }} - {{ p.price ?? p.prix }}€
            </option>
          </select>
        </label>

        <label>
          <span>Statut</span>
          <input v-model="form.status" placeholder="ex: brouillon, payé..." />
        </label>
      </div>

      <div class="actions">
        <button class="btn" type="submit">{{ edited?.id ? "Enregistrer" : "Créer" }}</button>
        <button class="btn ghost" type="button" @click="reset">Annuler</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, onMounted, watch } from "vue";
import { useClientStore } from "@/stores/clientStore";
import { usePlatStore } from "@/stores/platStore";
import { useCommandeStore } from "@/stores/commandeStore";

const props = defineProps({ edited: Object });
const emit = defineEmits(["saved", "cancel"]);

const clients = useClientStore();
const plats = usePlatStore();
const commandes = useCommandeStore();

const form = reactive({ client_id: "", plat_id: "", status: "" });

onMounted(async () => {
  if (!clients.list.length) await clients.fetchAll();
  if (!plats.list.length) await plats.fetchAll();
});

watch(
  () => props.edited,
  (v) => {
    if (v?.id) {
      form.client_id = v.client_id ?? v.client?.id ?? "";
      form.plat_id = v.plat_id ?? v.plat?.id ?? "";
      form.status = v.status ?? v.statut ?? "";
    } else {
      reset();
    }
  },
  { immediate: true }
);

async function submit() {
  const payload = { ...form };
  if (props.edited?.id) {
    await commandes.update(props.edited.id, payload);
  } else {
    await commandes.create(payload);
  }
  emit("saved");
  reset();
}

function reset() {
  form.client_id = "";
  form.plat_id = "";
  form.status = "";
  emit("cancel");
}
</script>

<style scoped>
.card { background:#1d1f23; border-radius:12px; padding:16px; }
.grid { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; }
label span { display:block; color:#8b949e; margin-bottom:6px; }
input, select { width:100%; padding:10px; border-radius:6px; border:1px solid #2a2e35; background:#151a21; color:#c9d1d9; }
.actions { display:flex; gap:10px; margin-top:12px; }
.btn { background:#f7b500; border:none; padding:8px 12px; border-radius:6px; color:#151a21; cursor:pointer; }
.btn.ghost { background:transparent; border:1px solid #2a2e35; color:#c9d1d9; }
</style>
