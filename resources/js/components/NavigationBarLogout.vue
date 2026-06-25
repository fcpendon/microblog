<template>
  <v-dialog
    v-model="dialog"
    max-width="400"
    persistent
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        plain
        v-bind="attrs"
        v-on="on"
      >
        <v-icon color="white">mdi-logout</v-icon>
        <div class="hidden-md-and-down">Logout</div>
      </v-btn>
    </template>
    <v-card>
      <v-card-title>Logout</v-card-title>
      <v-card-text>Are you sure you want to logout?</v-card-text>
      <v-card-actions class="pb-4">
        <v-btn
          text
          @click="dialog = false"
        >No
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          color="error"
          @click="logout"
          :disabled="disableLogout"
        >Yes
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import {mapActions} from 'vuex';

export default {
  data () {
    return {
      dialog: false,
      disableLogout: false
    }
  },
  methods: {
    ...mapActions({
      getUser: 'getUser',
      setAlertSuccess: 'setAlertSuccess',
    }),
    logout () {
      this.disableLogout = true;

      axios.post('/api/logout')
        .then(res => {
          this.dialog = false;
          this.getUser()
            .then(() => {
              this.setAlertSuccess(res.data.message);
              this.$router.push({name: 'login'});
            });
        })
        .catch(err => {
          console.log(err)
        });
    },
  }
}
</script>
