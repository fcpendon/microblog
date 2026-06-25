<template>
  <v-snackbar
    app
    v-model="$store.state.alert.show"
    :color="$store.state.alert.type"
    timeout="4000"
    top
  >{{ $store.state.alert.message }}
    <template v-slot:action="{ attrs }">
      <v-btn
        text
        v-bind="attrs"
        @click="hideAlert"
      >Close</v-btn>
    </template>
  </v-snackbar>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  methods: {
    ...mapActions({
      hideAlert: 'hideAlert',
    }),
  }
}
</script>
