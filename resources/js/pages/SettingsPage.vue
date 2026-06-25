<template>
  <v-container>
    <v-row justify="center" class="mt-8">
      <v-col cols="auto">
        <v-card width="700" tile>
          <v-card
            dark
            tile
            flat
            height="150"
            class="d-flex justify-center"
          >
            <v-card-title class="text-h2">Settings</v-card-title>
          </v-card>
          <v-divider></v-divider>
          <v-row no-gutters>
            <v-col cols="auto">
              <settings-user-avatar :image="user.image"></settings-user-avatar>
            </v-col>
            <v-col>
              <v-row no-gutters>
                <v-col>
                  <settings-user-details :user="user"></settings-user-details>
                </v-col>
              </v-row>
              <v-row
                v-if="user.accountType === 0"
                no-gutters
              >
                <v-col>
                  <settings-user-password :user="user"></settings-user-password>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import SettingsUserAvatar from '../components/SettingsUserAvatar';
import SettingsUserDetails from '../components/SettingsUserDetails';
import SettingsUserPassword from '../components/SettingsUserPassword';

export default {
  components: {
    SettingsUserPassword,
    SettingsUserAvatar,
    SettingsUserDetails
  },
  data () {
    return {
      user: {
        name: '',
        username: '',
        image: '',
        accountType: '',
      },
    }
  },
  created () {
    this.loadUserDetails();
  },
  methods: {
    loadUserDetails () {
      this.user = {
        name: this.$store.state.user.name,
        username: this.$store.state.user.username,
        image: this.$store.state.user.public_image_path,
        accountType: this.$store.state.user.account_type,
      }
    }
  }
}
</script>
