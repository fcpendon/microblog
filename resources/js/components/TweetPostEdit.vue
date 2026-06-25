<template>
  <v-dialog
    v-model="dialog"
    max-width="600"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        plain
        fab
        small
        v-bind="attrs"
        v-on="on"
      ><v-icon>mdi-pencil</v-icon>
      </v-btn>
    </template>
    <tweet-post-form v-if="dialog" :post="post" @closeDialog="closeDialog"></tweet-post-form>
  </v-dialog>
</template>

<script>
import TweetPostForm from './TweetPostForm';

export default {
  components: {
    TweetPostForm,
  },
  props: ['post'],
  data () {
    return {
      dialog: false,
    }
  },
  methods: {
    closeDialog () {
      this.dialog = false;
    }
  }
}
</script>
