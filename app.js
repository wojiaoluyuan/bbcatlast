{
  "name": "WordPress",
  "description": "WordPress is open source software you can use to create a beautiful website, blog, or app.",
  "keywords": [
	"wordpress",
	"cms",
	"bedrock",
	"boilerplate",
	"composer"
  ],
  "website": "https://wordpress.org/",
  "repository": "https://github.com/PhilippHeuer/wordpress-heroku",
  "success_url": "/wp/wp-admin",
  "scripts": {
	"postdeploy": [
	  "bin/scripts/postdeploy.sh"
	]
  },
  "stack": "heroku-18",
  "env": {
	"AUTH_KEY": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"SECURE_AUTH_KEY": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"LOGGED_IN_KEY": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"NONCE_KEY": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"AUTH_SALT": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"SECURE_AUTH_SALT": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"LOGGED_IN_SALT": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"NONCE_SALT": {
	  "description": "A secret key to increase security of stored information.",
	  "generator": "secret"
	},
	"HEROKU_APP_NAME": {
	  "required": false,
	  "description": "Only for automated deploys, please don't enter anything!"
	}
  },
  "formation": {
	"web": {
	  "quantity": 1,
	  "size": "free"
	}
  },
  "addons": [
	{
	  "plan": "jawsdb-maria:kitefin"
	},
	{
	  "plan": "scheduler:standard"
	},
	{
	  "plan": "papertrail:choklad"
	}
  ],
  "buildpacks": [
	{
	  "url": "https://github.com/heroku/heroku-buildpack-php"
	}
  ]
}
