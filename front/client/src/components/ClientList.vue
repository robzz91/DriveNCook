<template>
  <div class="card">
    <div class="card-header">
      <h3>Liste des clients</h3>
      <button class="btn" @click="store.fetchAll()" :disabled="store.loading">Recharger</button>
    </div>

    <div v-if="store.error" class="alert">{{ store.error }}</div>

    <table class="table" v-if="store.list.length">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Email</th>
          <th class="right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="c in store.list" :key="c.id">
          <td>{{ c.id }}</td>
          <td>{{ c.name || c.nom }}</td>
          <td>{{ c.email }}</td>
          <td class="right">
            <button class="btn ghost" @click="edit(c)">Modifier</button>
            <button class="btn danger" @click="remove(c.id)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="muted">Aucun client.</p>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useClientStore } from "@/stores/clientStore";

const emit = defineEmits(["edit"]);
const store = useClientStore();

onMounted(() => {
  if (!store.list.length) store.fetchAll();
});

function edit(c) {
  emit("edit", c);
}
function remove(id) {
  if (confirm("Supprimer ce client ?")) store.remove(id);
}
</script>

<style scoped>
.card { background: #1d1f23; border-radius: 12px; padding: 16px; }
.card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px; }
.table { width:100%; border-collapse: collapse; }
.table th, .table td { padding: 10px; border-bottom: 1px solid #2a2e35; }
.table th { text-align: left; color: #c9d1d9; }
.right { text-align: right; }
.btn { background:#f7b500; border:none; padding:8px 12px; border-radius:6px; color:#151a21; cursor:pointer; }
.btn.ghost { background: transparent; border:1px solid #2a2e35; color:#c9d1d9; }
.btn.danger { background:#e34d4d; color:white; }
.alert { background:#3a1f1f; color:#ffcccc; padding:8px; border-radius:6px; margin-bottom: 10px; }
.muted { color:#8b949e; }
</style>
