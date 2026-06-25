<template>
  <v-dialog
    v-model="dialog"
    max-width="400"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        plain
        fab
        small
        v-bind="attrs"
        v-on="on"
      ><v-icon>mdi-delete</v-icon>
      </v-btn>
    </template>
    <v-card>
      <v-card-title>Delete Post</v-card-title>
      <v-card-text>Are you sure you want to delete this post?</v-card-text>
      <v-card-actions class="pb-4">
        <v-btn
          text
          @click="dialog = false"
        >No
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          color="error"
          @click="deletePost"
          :disabled="disableDelete"
        >Yes
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import {mapActions} from 'vuex';

export default {
  props: ['post'],
  data () {
    return {
      dialog: false,
      disableDelete: false,
    }
  },
  methods: {
    ...mapActions({
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    deletePost () {
      this.disableDelete = true;

      axios.delete('/api/posts/' + this.post.id)
        .then(res => {
          this.setAlertSuccess(res.data.message);
          this.$root.$emit('reloadTweets');
        })
        .catch(err => {
          this.setAlertError(err.response.data);
        })
        .finally(() => {
          this.disablePost = false;
        });
    },
  }
}
</script>
