# How to add a new provider
Source: https://docs.postiz.com/configuration/create-provider

How to add a new provider to Postiz

# Steps to implement a new provider

1. **The backend logic:**
   * Define DTO for the settings of the provider
   * Generate an authentication URL
   * Authenticate the user from the callback
   * Refresh the user token

2. **The frontend logic:**
   * Implement the settings page
   * Implement the preview page
   * Upload the provider image

## Social Media

### Backend

For our example, we will use the X provider.

<Steps>
  <Step title="Create a DTO for provider settings">
    Head over to `nestjs-libraries/src/dtos/posts/providers-settings` and create a new file `x-provider-settings.dto.ts`

    <Note>
      You don't have to create a DTO if there are no settings
    </Note>

    Once created head over to `nestjs-libraries/src/dtos/posts/providers-settings/all.providers.settings.ts` and add the new DTO.

    Head to `libraries/nestjs-libraries/src/dtos/posts/create.post.dto.ts`, look for the discriminator and add another line in the format of:

    ```typescript  theme={null}
    { value: DTOClassName, name: 'providerName' },
    ```
  </Step>

  <Step title="Create the provider file">
    Head over to `libraries/nestjs-libraries/src/integrations/social` and create a new provider file `providerName.provider.ts`

    For oAuth2 providers, the content of the file should look like this:

    ```typescript  theme={null}
    import {
      AuthTokenDetails,
      PostDetails,
      PostResponse,
      SocialProvider,
    } from '@gitroom/nestjs-libraries/integrations/social/social.integrations.interface';

    export class XProvider implements SocialProvider {
      identifier = 'providerName';
      name = 'Provider Name';
      
      async refreshToken(refreshToken: string): Promise<AuthTokenDetails> {
        // ...refresh the token
      }

      async generateAuthUrl() {
        // ...generate the auth url
      }

      async authenticate(params: { code: string; codeVerifier: string }) {
        // ...authenticate the user
      }

      async post(
        id: string,
        accessToken: string,
        postDetails: PostDetails<DTOClassName>[]
      ): Promise<PostResponse[]> {
        // ...post the content
      }
    }
    ```

    Take a look at the existing providers to see how to implement the methods.
  </Step>

  <Step title="Register with Integration Manager">
    Open `libraries/nestjs-libraries/src/integrations/integration.manager.ts` and add the new provider to either `socialIntegrationList` (oAuth2) or `articleIntegrationList` (Token)
  </Step>
</Steps>

### Custom functions

You might want to create custom functions for the providers for example: get available orgs, get available pages, etc.

You can create a public function in the provider for example `organizations` and later call it from a special hook from the frontend.

***

### Frontend

<Steps>
  <Step title="Create provider component">
    Head over to `apps/frontend/src/components/launches/providers` and create a new folder with the providerName.

    Add a new file `providerName.provider.tsx` with the following content:

    ```typescript  theme={null}
    import { FC } from 'react';
    import { withProvider } from '@gitroom/frontend/components/launches/providers/high.order.provider';
    import { useSettings } from '@gitroom/frontend/components/launches/helpers/use.values';
    import { useIntegration } from '@gitroom/frontend/components/launches/helpers/use.integration';

    const ProviderPreview: FC = () => {
      const { value } = useIntegration();
      const settings = useSettings();

      return (
        // ...Preview
      );
    };

    const ProviderSettings: FC = () => {
      const form = useSettings();
      const { date } = useIntegration();
      return (
        // ...Settings
      );
    };

    export default withProvider(DevtoSettings, DevtoPreview, DTOClassName);
    ```
  </Step>

  <Step title="Use custom provider functions (optional)">
    If you want to use a custom function for the provider you can use the `useCustomProviderFunction` hook.

    ```typescript  theme={null}
    import { useCustomProviderFunction } from '@gitroom/frontend/components/launches/helpers/use.custom.provider.function';
    import { useCallback } from 'react';

    const customFunc = useCustomProviderFunction();

    // and use it like that:
    const getOrgs = useCallback(() => {
      customFunc.get('organizations', {
        anyKey: 'anyValue'
      })
    }, []);
    ```

    It will automatically interact with the right provider saved for the user.

    You can look at the other integrations to understand what data to put inside.
  </Step>

  <Step title="Register the provider">
    Open `apps/frontend/src/components/launches/providers/show.all.providers.tsx` and add the new provider to the list.

    ```typescript  theme={null}
    {identifier: 'providerName', component: DefaultImportFromHighOrderProvider},
    ```
  </Step>
</Steps>


# Docker Compose Configuration
Source: https://docs.postiz.com/configuration/docker

How to configure your docker-compose file for Postiz

You will often see, when for example configuring providers, that the environment variables will look like this:

```env  theme={null}
INSTAGRAM_CLIENT_ID=12345678901234567890
```

You have 2 options on how to set these variables in your `docker-compose.yml` file.

## Option 1: Direct in docker-compose.yml

You can set them directly in the `environment` section of the service.

```yaml  theme={null}
services:
  postiz:
    environment:
      YOUR_ENV_VAR: "value"
      YOUR_OTHER_ENV_VAR: "value"
```

## Option 2: Using a .env file

You can use a `.env` file to set the variables.

**docker-compose.yml:**

```yaml  theme={null}
services:
  postiz:
    env_file:
      - .env
```

**.env:**

```env  theme={null}
YOUR_ENV_VAR=value
YOUR_OTHER_ENV_VAR=value
```

## Option 3: Combine both

You can also use both!

**docker-compose.yml:**

```yaml  theme={null}
services:
  postiz:
    environment:
      YOUR_ENV_VAR: "value"
    env_file:
      - .env
```

**.env:**

```env  theme={null}
YOUR_OTHER_ENV_VAR=value
```

<Note>
  When using an .env file, you will need to transfer all environment variables from the docker-compose.yml file to the .env file.
  An .env file will override any variables set in the .yml file.
  Using an .env file for the DB / Redis won't be necessary.
</Note>


# Email Notifications
Source: https://docs.postiz.com/configuration/emails

How to send notifications to users

Postiz supports two email providers: Resend and NodeMailer (SMTP).

If you have an email provider configured, then new users will require activation.

```env  theme={null}
EMAIL_PROVIDER: "resend|nodemailer"
```

You must also set the sender name and email address for all providers as follows;

```env  theme={null}
EMAIL_FROM_NAME: "Postiz Emailer"
EMAIL_FROM_ADDRESS: "postiz@example.com"
```

## Resend

Postiz uses Resend to send email notifications to users. If this key is set, users
will also require activation.

<Steps>
  <Step title="Register on Resend">
    Register to [Resend](https://resend.com), and connect your domain.
  </Step>

  <Step title="Copy your API Key">
    Copy your API Key from the Resend control panel.
  </Step>

  <Step title="Edit your .env file">
    Open the .env file and edit the following line.

    ```env  theme={null}
    EMAIL_PROVIDER="resend"
    RESEND_API_KEY="<your-api-key-here>"
    ```
  </Step>
</Steps>

## NodeMailer (SMTP)

This is an alternative to Resend. You can use NodeMailer, which is simply a SMTP library, to connect to any SMTP server.

```env  theme={null}
EMAIL_PROVIDER: "nodemailer"
EMAIL_HOST: "smtp.gmail.com" # smtp host if you choose nodemailer
EMAIL_PORT: "465" # smtp port if you choose nodemailer
EMAIL_SECURE: "true" # smtp secure if you choose nodemailer
EMAIL_USER: "user" # smtp user if you choose nodemailer
EMAIL_PASS: "pass" # smtp pass if you choose nodemailer
```


# OIDC Configuration
Source: https://docs.postiz.com/configuration/oauth

How to configure OIDC for Postiz

<Warning>
  **Warning:** With the actual implementation of the OIDC provider, GitHub / Google login provider will be disabled.
</Warning>

If you want to use OAuth/OIDC, please follow the instructions below.

We will use [Authentik](https://goauthentik.io/) as an OIDC provider example, with base URL `https://authentik.example.com`

<Steps>
  <Step title="Create an Application/Provider on the Authentik side">
    You will find the following important information:

    * `redirect_uri` => `https://postiz.yourserver.com/settings`
    * `client_id` => `randomclientid`
    * `client_secret` => `randomclientsecret`
    * `auth_url` => `https://authentik.example.com/application/o/authorize`
    * `token_url` => `https://authentik.example.com/application/o/token`
    * `userinfo_url`=> `https://authentik.example.com/application/o/userinfo`

    <Note>
      The same information needs to be configured on other OIDC providers such as Keycloak, Dex, etc.
    </Note>
  </Step>

  <Step title="Configure POSTIZ_GENERIC_OAUTH">
    ```env  theme={null}
    POSTIZ_GENERIC_OAUTH="true"
    ```

    Set to `true` to enable OIDC login.
  </Step>

  <Step title="Configure display name">
    ```env  theme={null}
    NEXT_PUBLIC_POSTIZ_OAUTH_DISPLAY_NAME="Authentik"
    ```

    Will display the name of the OIDC provider on the login page.
  </Step>

  <Step title="Configure logo URL">
    ```env  theme={null}
    NEXT_PUBLIC_POSTIZ_OAUTH_LOGO_URL="https://raw.githubusercontent.com/walkxcode/dashboard-icons/master/png/authentik.png"
    ```

    Will display the logo of the OIDC provider on the login page button.
  </Step>

  <Step title="Configure POSTIZ_OAUTH_URL">
    ```env  theme={null}
    POSTIZ_OAUTH_URL="https://authentik.example.com"
    ```

    The base URL of the OIDC provider.
  </Step>

  <Step title="Configure POSTIZ_OAUTH_AUTH_URL">
    ```env  theme={null}
    POSTIZ_OAUTH_AUTH_URL="https://authentik.example.com/application/o/authorize/"
    ```

    The authorization URL of the OIDC provider.
  </Step>

  <Step title="Configure POSTIZ_OAUTH_TOKEN_URL">
    ```env  theme={null}
    POSTIZ_OAUTH_TOKEN_URL="https://authentik.example.com/application/o/token/"
    ```

    The token URL of the OIDC provider.
  </Step>

  <Step title="Configure POSTIZ_OAUTH_USERINFO_URL">
    ```env  theme={null}
    POSTIZ_OAUTH_USERINFO_URL="https://authentik.example.com/application/o/userinfo/"
    ```

    The userinfo URL of the OIDC provider.
  </Step>

  <Step title="Configure POSTIZ_OAUTH_CLIENT_ID">
    ```env  theme={null}
    POSTIZ_OAUTH_CLIENT_ID="randomclientid"
    ```

    The client ID of the OIDC provider.
  </Step>

  <Step title="Configure POSTIZ_OAUTH_CLIENT_SECRET">
    ```env  theme={null}
    POSTIZ_OAUTH_CLIENT_SECRET="randomclientsecret"
    ```

    The client secret of the OIDC provider.
  </Step>
</Steps>


# R2 Configuration
Source: https://docs.postiz.com/configuration/r2

How to use Cloudflare R2 for file storage

If you do not wish to (or can't) use local storage, an alternative way to upload images is to configure R2. It's free.

<Steps>
  <Step title="Create account and login to the console">
    Go to the [Cloudflare Dashboard](https://dash.cloudflare.com/r2/overview), and register if needed, then login.
  </Step>

  <Step title="Create a new Bucket">
    In the dashboard sidebar, and head to the R2 page.

    <img src="https://github.com/user-attachments/assets/6d540494-b439-4715-9e8c-615e1f734aef" alt="R2 Page" />

    Create a new Bucket.

    * Choose Automatic
    * Choose Standard

    <img src="https://github.com/user-attachments/assets/f679ff9f-1a9c-4d09-b663-2fa2baadea93" alt="Create Bucket" />
  </Step>

  <Step title="Create your R2 Token">
    Create your R2 Token by going to R2 Object Storage:

    <img src="https://i.imgur.com/yVJwVBb.png" alt="R2 Object Storage" />

    Click on the API dropdown, and select [Manage API tokens](https://dash.cloudflare.com/?to=/:account/r2/api-tokens):

    <img src="https://i.imgur.com/PmzyGeQ.png" alt="Manage API tokens" />

    Copy your Account ID for later, and click on "Create an API token":

    <img src="https://i.imgur.com/hvsGnWI.png" alt="Create API Token" />

    Create an Account API token:

    <img src="https://i.imgur.com/MvZsYfC.png" alt="Account API Token" />

    Under "Permissions" choose "Object Read & Write" and under "Specify bucket(s)" search for your created Bucket.

    <img src="https://github.com/user-attachments/assets/30991d43-9920-47d4-9c53-bde619974009" alt="Permissions" />
  </Step>

  <Step title="Copy your credentials">
    After the R2 Token is created, copy your "Access Key ID" and "Secret Access Key":

    <img src="https://github.com/user-attachments/assets/d4b4218f-fe20-47ec-937a-0d516fa74c64" alt="Copy Credentials" />

    Paste the respective information into your .env environment.

    ```env  theme={null}
    CLOUDFLARE_ACCOUNT_ID="accountId"
    CLOUDFLARE_ACCESS_KEY="accessKey"
    CLOUDFLARE_SECRET_ACCESS_KEY="secretAccessKey"
    CLOUDFLARE_BUCKETNAME="bucketName"
    CLOUDFLARE_REGION="region (like wnam)"
    ```
  </Step>

  <Step title="Configure Custom Domain and CORS policies">
    Go to configuration and connect a custom domain (if you don't have one, you can use the one that CloudFlare provides.)
    Add it to your .env file.

    ```env  theme={null}
    CLOUDFLARE_BUCKET_URL="https://customdomain.com"
    ```

    <img src="https://github.com/user-attachments/assets/a7bff532-f69b-4095-bb66-e46b7d3b14bd" alt="Custom Domain" />

    Click to edit the CORS policy and add the following JSON:

    ```json  theme={null}
    [
      {
        "AllowedOrigins": [
          "http://localhost:4200",
          "https://yourDomain.com"
        ],
        "AllowedMethods": [
          "GET",
          "POST",
          "HEAD",
          "PUT",
          "DELETE"
        ],
        "AllowedHeaders": [
          "Authorization",
          "x-amz-date",
          "x-amz-content-sha256",
          "content-type"
        ],
        "ExposeHeaders": [
          "ETag",
          "Location"
        ],
        "MaxAgeSeconds": 3600
      }
    ]
    ```

    <img src="https://github.com/user-attachments/assets/1f84fe71-dd2d-4ca6-92bf-6193d36c9b54" alt="CORS Policy" />
  </Step>
</Steps>


# Configuration Reference
Source: https://docs.postiz.com/configuration/reference

Environment variables reference for Postiz

At the moment, Postiz is entirely configured by environment variables. It is
important to understand that any configuration change to environment variables
will require an application restart.

An example file of the most used configuration settings can be found here; [example postiz.env file](https://raw.githubusercontent.com/gitroomhq/postiz-app/main/.env.example)

## Required Settings

### `DATABASE_URL`

eg: `postgresql://postiz-user:postiz-password@localhost:5432/postiz-db-local`

Postgres is not strictly necessary, Postiz uses Prisma to connect to the database, so technically mariadb or other databases could be used.

### `REDIS_URL`

eg: `redis://localhost:6379`

### `JWT_SECRET`

A random string that should be unique for every installation, this is used to secure your JWT auth tokens.

### `FRONTEND_URL`

eg: `http://postiz.example.lan:4200`

### `NEXT_PUBLIC_BACKEND_URL`

eg: `http://postiz.example.lan:3000`

### `BACKEND_INTERNAL_URL`

If running everything in the same host/container: `http://localhost:3000`

## Optional settings

Refer to the [example postiz.env file](https://raw.githubusercontent.com/gitroomhq/postiz-app/main/.env.example) for additional configuration options.

### OAUTH

See [this page](/configuration/oauth) for reference.

### DISABLE\_REGISTRATION

eg: `true`

This will only allow a single user registration, and then after that the sign-up page will be disabled. Useful for self-hosting where you want complete control over users.

<Warning>
  This will disable OIDC / OAuth!
</Warning>

### DISABLE\_IMAGE\_COMPRESSION

This will disable the image compression if set to "true".

## Social Media keys

See the "providers" section of the documentation for the provider you want to configure,
for details about how to set that up.


# Contributing
Source: https://docs.postiz.com/contributing

How to contribute to Postiz

# Contributing

Contributions are welcome - code, docs, whatever it might be!
If this is your first contribution to an Open Source project or you're a core maintainer of multiple projects, your time and interest in contributing to this project is most welcome.

## Pre-Requirements

* GitHub Account
* Experience with PRs, Forks, Branches
* A Computer (Mac, Linux, Windows etc.)
* (Optional) Joined the discord server

## Read the developers guide

The documentation site has a [developer guide](/developer-guide).
That guide provides you a good understanding of the project structure, and how to setup your development environment. Read this document after you have read that guide.
This page is intended to provide you a good understanding of how to submit your first contribution.

## Write code with others

This is an open source project, with an open and welcoming community that is always keen to welcome new contributors.
We recommend the two best ways to interact with the community are:

* **GitHub issues**: To discuss more slowly, or longer-written messages.
* **[Discord chat](https://discord.postiz.com)**: To chat with people and a quicker feedback.

## Types of Contributions

Contributions can include:

* **Code improvements:** Fixing bugs or adding new features.
* **Documentation updates:** Enhancing clarity or adding missing information.
* **Feature requests:** Suggesting new capabilities or integrations.
* **Bug reports:** Identifying and reporting issues.

## How to contribute

This project follows a Fork/Feature Branch/Pull Request model. If you're not familiar with this, here's how it works:

<Steps>
  <Step title="Fork the project">
    Create a personal copy of the repository on your GitHub account.
  </Step>

  <Step title="Clone your fork">
    Bring a copy of your fork to your local machine.

    ```bash  theme={null}
    git clone https://github.com/YOUR_USERNAME/postiz.git
    ```
  </Step>

  <Step title="Create a new branch">
    Start a new branch for your changes.

    ```bash  theme={null}
    git checkout -b feature/your-feature-name
    ```
  </Step>

  <Step title="Make your changes">
    Implement the changes you wish to contribute.
  </Step>

  <Step title="Push your changes">
    Push your changes to your fork.

    ```bash  theme={null}
    git push -u origin feature/your-feature-name
    ```
  </Step>

  <Step title="Create a pull request">
    Create a draft pull request with the name of the feature.
  </Step>

  <Step title="Test your changes (optional)">
    For big changes, use the [Developer environment](/installation/development).
  </Step>

  <Step title="Mark ready for review">
    Mark your PR ready for review after testing and wait for it to be merged.
  </Step>
</Steps>


# Developer Guide
Source: https://docs.postiz.com/developer-guide

How to get started developing with Postiz

## Understand how to develop with Postiz

## How to setup your development environment

This page explains [How to setup your development environment](/installation/development).

## Architecture Overview

Before getting started with development, have a good read of the [architecture overview](/howitworks). This will give you a good understanding of how the project is structured and how the different parts of the project interact with each other.

## Repository Overview

Postiz is an open-source project, and the source code is available on [GitHub](https://github.com/gitroomhq/postiz-app).

The project is generally built using scripts in the `package.json` file with npm. The main scripts are:

* `npm run dev` - Starts the development server
* `npm run prisma-generate` - Generates the Prisma client
* `npm run prisma-db-push` - Pushes the database schema to the database

The entire project is built under [NX](https://nx.dev/) to have a monorepo with multiple projects.

Unlike other NX project, this project has one `.env` file that is shared between all the apps.
It makes it easier to develop and deploy the project.

### Frontend

The frontend is built with [NextJS](https://nextjs.org/) and [TailwindCSS](https://tailwindcss.com/).

### Backend

The backend is built with [NestJS](https://nestjs.com/) with a basic architecture of controllers, services, repositories and dtos.

It uses [Prisma](https://www.prisma.io/) as an ORM to interact with the database.
By default Prisma uses [Postgres](https://www.postgresql.org/) as a database, but it can be easily changed to any other database since there are no native queries.

It uses Redis to schedule posts and run background jobs.

### Cron

cron is built with [NestJS](https://nestjs.com/) and share components with the backend.

### Worker

worker is built with [NestJS](https://nestjs.com/) and share components with the backend.

## Contributors Guide

The Postiz [contributors guide](/contributing) is contained in the main repository. It provides information on how to contribute to the project, mainly the format for how to submit a pull request.


# How it works
Source: https://docs.postiz.com/howitworks

Learn the architecture of the project

## Architecture

Postiz is composed of 4 main services and 3 external services - all 4 of the main services typically run within a **single docker container**, and talk to each other through HTTP. Those 4 main servers typically talk to other containers, running the external services - the SQL Database, Redis Queue and Storage.

```mermaid  theme={null}
flowchart LR;
    classDef ext fill:#8ED14F,color:black,stroke:#fff
    classDef svc fill:#9900e6,color:white,stroke:#fff

    frontend[Frontend Service]:::svc
    backend[Backend Service]:::svc
    cron[Cron Service]:::svc
    workers[Worker Service]:::svc
    redis[Redis Queue]:::ext
    db[SQL Database]:::ext
    storage[Storage]:::ext

	frontend --> backend
    backend --> db
    backend --> redis
    cron <--> redis
    workers --> storage
    workers --> db
    redis --> workers
    backend --> storage

```

* [Frontend](#frontend) - Provides the Web user interface, talks to the Backend.

* [Backend](#backend) - Does all the real work, provides an API for the frontend, and posts work to the redis queue.

* [Workers](#worker) - Consumes work from the Redis Queue.

* [Cron](#cron) - Run jobs at scheduled times.

* [Redis Queue](#redis) - A simple queue for the workers to consume work from.

* [SQL Database](#db) - Stores all the data, Postgres is typically used, but any SQL database can be used.

* [Storage](#storage) - Stores all the files, this used to be CloudFlare R2 as the default, but now it's just a local file system.

### Frontend

The frontend is the part that you see, the web interface.

It relies on the backend to:

* Schedule posts
* Show analytics
* Manage users

### Backend

The backend is the "brain" of Postiz, and coordinates all the work. Typically the SQL database it talks to is Postgres, but other databases can be used.

### Cron

The cron service does the following;

* Refresh tokens from different social media platforms.
* Check for trending change every hour and inform users about it.
* Sync the amount of stars for every repository at the end of the day.

### Worker

The worker services does the following;

* Post scheduled posts to social media platforms.
* Perform multiple jobs coming from the cron.


# Coolify
Source: https://docs.postiz.com/installation/coolify

How to install Postiz on Coolify

<Snippet file="earlydoc.mdx" />

<Warning>
  **Warning:** This current Documentation does **not** work in Coolify. We are working on a fix for this Issue.
</Warning>

<Snippet file="installation-pre-reqs.mdx" />

<Steps>
  <Step title="Create a new project">
    * **Name**: Postiz
    * Select the "production" environment.
  </Step>

  <Step title="Add Postiz as a Docker Compose resource">
    Copy the Coolify docker-compose file here - and be careful to read the comments about variables you must change!

    ```yaml  theme={null}
    services:
      postiz:
        image: ghcr.io/gitroomhq/postiz-app:latest
        container_name: postiz
        restart: always
        environment:
          # You must change these. `yourServerAddress` this needs to be exactly the URL you're accessing Postiz on.
          MAIN_URL: "https://postiz.your-server.com"
          FRONTEND_URL: "https://postiz.your-server.com"
          NEXT_PUBLIC_BACKEND_URL: "https://postiz.your-server.com/api"
          JWT_SECRET: "random string that is unique to every install - just type random characters here!"

          # These defaults are probably fine, but if you change your user/password, update it in the 
          # postiz-postgres or postiz-redis services below.
          DATABASE_URL: "postgresql://postiz-user:postiz-password@postiz-postgres:5432/postiz-db-local"
          REDIS_URL: "redis://postiz-redis:6379"
          BACKEND_INTERNAL_URL: "http://localhost:3000"
          IS_GENERAL: "true" # Required for self-hosting.

          # The container images are pre-configured to use /uploads for file storage.
          # You probably should not change this unless you have a really good reason!
          STORAGE_PROVIDER: "local"
          UPLOAD_DIRECTORY: "/uploads"
          NEXT_PUBLIC_UPLOAD_DIRECTORY: "/uploads"
        volumes:
          - postiz-config:/config/
          - postiz-uploads:/uploads/
        ports:
          - 5000:5000
        networks:
          - postiz-network
        labels:
          - "traefik.enable=true"
          - "traefik.https.routers.<unique_router_name>.rule=Host(`coolify.io`) && PathPrefix(`/`)"
          - "traefik.https.routers.<unique_router_name>.entryPoints=https"
        depends_on:
          postiz-postgres:
            condition: service_healthy
          postiz-redis:
            condition: service_healthy

      postiz-postgres:
        image: postgres:14.5
        container_name: postiz-postgres
        restart: always
        environment:
          POSTGRES_PASSWORD: postiz-password
          POSTGRES_USER: postiz-user
          POSTGRES_DB: postiz-db-local
        volumes:
          - postgres-volume:/var/lib/postgresql/data
        ports:
          - 5432:5432
        networks:
          - postiz-network
        healthcheck:
          test: pg_isready -U postiz-user -d postiz-db-local
          interval: 10s
          timeout: 3s
          retries: 3
      postiz-redis:
        image: redis:7.2
        container_name: postiz-redis
        restart: always
        ports:
          - 6379:6379
        healthcheck:
          test: redis-cli ping
          interval: 10s
          timeout: 3s
          retries: 3
        volumes:
          - postiz-redis-data:/data
        networks:
          - postiz-network


    volumes:
      postgres-volume:
        external: false

      postiz-redis-data:
        external: false

      postiz-config:
        external: false

    networks:
      postiz-network:
        external: false
    ```

    Save the configuration.
  </Step>

  <Step title="Check the configuration">
    * In the "Service Stack" tab;
      * Suggest changing the "Service Name" to just "postiz" for clarity.
      * Click the "Connect to Predefined Network" tab.
      * Check that 3 services are defined - `postiz`, `postiz-postgres` and `postiz-redis`.
    * In the "Storages" tab, check that the 3 related volumes are created.
  </Step>

  <Step title="Start the deployment">
    Postiz is approximately 2.5Gb and several container layers, it will take some time to download, be patient. You should see "Downloading" and "Extracting" messages. The Postiz dependencies are almost 200k files, and this can take a while.

    Hopefully you will see a message like `Container postiz-dssc8gc880s88cg08cck884s  Started.` in the logs. Once you see this message, close the startup logs view.

    Check that the services are running in the "Service Stack" tab, and make sure they are not constantly restarting, or failing to start.
  </Step>
</Steps>


# Dev Container
Source: https://docs.postiz.com/installation/devcontainer

Install Postiz using Dev Container

<Snippet file="earlydoc.mdx" />

<Snippet file="installation-recommended-options.mdx" />

```bash  theme={null}
npm install -g @devcontainers/cli
devcontainer up
```


# Development Environment
Source: https://docs.postiz.com/installation/development

Set up Postiz for local development

Only use this method if you cannot use docker or want to develop on Postiz.
[Docker-Compose](/installation/docker-compose) is the recommended Method.

## Tested configurations

* MacOS
* Linux (Fedora 40)

Naturally you can use these instructions to setup a development environment on any platform, but there may not be much experience in the community to help you with any issues you may encounter.

### Warning about Windows

Several users using Windows (and WSL) have reported issues with the setup. This is not well tested as the main developers of the project do not use Windows/WSL for development. If you are using Windows and encounter issues, please do not try to get support, as we aren't able to support you.

<Snippet file="installation-pre-reqs.mdx" />

### Prerequisite Local Services

* **Node.js** - for running the code! (version 18+)
* **PostgreSQL** - or any other SQL database (instructions below suggest Docker)
* **Redis** - for handling worker queues (instructions below suggest Docker)

We have some messages from users who are using Windows, which should work, but they are not tested well yet.

## Installation Instructions

### NodeJS (version 18+)

A complete guide of how to install NodeJS can be found [here](https://nodejs.org/en/download/).

### PostgreSQL (or any other SQL database) & Redis

You can choose **Option A** to **Option B** to install the database.

#### Option A) Postgres and Redis as Single containers

You can install [Docker](https://www.docker.com/products/docker-desktop) and run:

```bash  theme={null}
docker run -e POSTGRES_USER=root -e POSTGRES_PASSWORD=your_password --name postgres -p 5432:5432 -d postgres
docker run --name redis -p 6379:6379 -d redis
```

#### Option B) Postgres and Redis as docker-compose

Download the [docker-compose.yaml file here](https://raw.githubusercontent.com/gitroomhq/postiz-app/main/docker-compose.dev.yaml),
or grab it from the repository in the next step.

```bash  theme={null}
docker compose -f "docker-compose.dev.yaml" up
```

## Build Postiz

<Steps>
  <Step title="Clone the repository">
    ```bash  theme={null}
    git clone https://github.com/gitroomhq/postiz-app.git
    ```
  </Step>

  <Step title="Set environment variables">
    Copy the `.env.example` file to `.env` and fill in the values

    ```bash  theme={null}
    # Required Settings
    DATABASE_URL="postgresql://postiz-user:postiz-password@localhost:5432/postiz-db-local"
    REDIS_URL="redis://localhost:6379"
    JWT_SECRET="random string for your JWT secret, make it long"
    FRONTEND_URL="http://localhost:4200"
    NEXT_PUBLIC_BACKEND_URL="http://localhost:3000"
    BACKEND_INTERNAL_URL="http://localhost:3000"

    # Optional. Your upload directory path if you host your files locally.
    UPLOAD_DIRECTORY="/opt/postiz/uploads/"

    # Optional: your upload directory slug if you host your files locally.
    NEXT_PUBLIC_UPLOAD_STATIC_DIRECTORY=""

    # Your email provider, optional
    EMAIL_PROVIDER="resend|nodemailer"
    RESEND_API_KEY="re_1234567890" # api key if you choose resend
    EMAIL_HOST="smtp.gmail.com" # smtp host if you choose nodemailer
    EMAIL_PORT="465" # smtp port if you choose nodemailer
    EMAIL_SECURE="true" # smtp secure if you choose nodemailer
    EMAIL_USER="user" # smtp user if you choose nodemailer
    EMAIL_PASS="pass" # smtp pass if you choose nodemailer

    ## These are dummy values, you must create your own from Cloudflare.
    ## Remember to set your public internet IP address in the allow-list for the API token.
    CLOUDFLARE_ACCOUNT_ID="QhcMSXQyPuMCRpSQcSYdEuTYgHeCXHbu"
    CLOUDFLARE_ACCESS_KEY="dcfCMSuFEeCNfvByUureMZEfxWJmDqZe"
    CLOUDFLARE_SECRET_ACCESS_KEY="zTTMXBmtyLPwHEdpACGHgDgzRTNpTJewiNriLnUS"
    CLOUDFLARE_BUCKETNAME="postiz"
    CLOUDFLARE_BUCKET_URL="https://QhcMSXQyPuMCRpSQcSYdEuTYgHeCXHbu.r2.cloudflarestorage.com/"
    CLOUDFLARE_REGION="auto"

    # Social Media API Settings
    X_API_KEY="Twitter API key for normal oAuth not oAuth2"
    X_API_SECRET="Twitter API secret for normal oAuth not oAuth2"
    LINKEDIN_CLIENT_ID="Linkedin Client ID"
    LINKEDIN_CLIENT_SECRET="Linkedin Client Secret"
    REDDIT_CLIENT_ID="Reddit Client ID"
    REDDIT_CLIENT_SECRET="Linkedin Client Secret"
    GITHUB_CLIENT_ID="GitHub Client ID"
    GITHUB_CLIENT_SECRET="GitHub Client Secret"

    # AI
    OPENAI_API_KEY="OpenAI API key"

    # Developer Settings
    NX_ADD_PLUGINS=false
    IS_GENERAL="true" # required for now
    ```
  </Step>

  <Step title="Install the dependencies">
    ```bash  theme={null}
    pnpm install
    ```
  </Step>

  <Step title="Generate the prisma client and run the migrations">
    ```bash  theme={null}
    pnpm run prisma-db-push
    ```
  </Step>

  <Step title="Run the project">
    ```bash  theme={null}
    pnpm run dev
    ```
  </Step>
</Steps>

If everything is running successfully, open [http://localhost:4200](http://localhost:4200) in your browser!

If everything is not running - you had errors in the steps above, please head over to our [support](/support) page.

## Next Steps

<CardGroup cols={2}>
  <Card title="Configure uploads" icon="cloud-arrow-up" href="/configuration/r2">
    Set up R2 for file storage
  </Card>

  <Card title="Architecture" icon="diagram-project" href="/howitworks">
    Learn the architecture of the project
  </Card>

  <Card title="Email notifications" icon="envelope" href="/configuration/emails">
    Set up email for notifications
  </Card>

  <Card title="Providers" icon="plug" href="/providers/overview">
    Set up providers such as LinkedIn, X and Reddit
  </Card>
</CardGroup>


# Docker
Source: https://docs.postiz.com/installation/docker

Install Postiz using Docker standalone

<Snippet file="installation-recommended-options.mdx" />

<Snippet file="installation-pre-reqs.mdx" />

## Set environment variables

Postiz configuration is entirely via environment variables for now. You might be used to setting environment variables when starting containers,
however postiz needs a LOT of environment variables, so setting these on command line or in a docker-compose is probably not practical for long
term maintainability.

It is recommended to use a `.env` file, which the Postiz containers look for in /config. Docker will automatically create this file for you on a
docker volume the first time you start up Postiz.

The default .env file can be found here; [example .env file](https://raw.githubusercontent.com/gitroomhq/postiz-app/main/.env.example)

## Create the container

This example below shows how to create the Postiz container on the command line.

Note that you will need to replace the `./config` with the path to your config directory. You will also need Postgres and Redis running.

```bash  theme={null}
docker create --name postiz -v postiz-uploads:/uploads/ -v postiz-config:/config/ -p 5000:5000 ghcr.io/gitroomhq/postiz-app:latest
```

<Snippet file="docker-envvar-apps.mdx" />

## Next Steps

<CardGroup cols={2}>
  <Card title="Configure uploads" icon="cloud-arrow-up" href="/configuration/r2">
    Set up R2 for file storage
  </Card>

  <Card title="Architecture" icon="diagram-project" href="/howitworks">
    Learn the architecture of the project
  </Card>

  <Card title="Email notifications" icon="envelope" href="/configuration/emails">
    Set up email for notifications
  </Card>

  <Card title="Providers" icon="plug" href="/providers/overview">
    Set up providers such as LinkedIn, X and Reddit
  </Card>
</CardGroup>


# Docker Compose
Source: https://docs.postiz.com/installation/docker-compose

Install Postiz using Docker Compose

<Note>
  Watch the Tutorial for docker-compose install: [https://m.youtube.com/watch?v=A6CjAmJOWvA\&t=5s](https://m.youtube.com/watch?v=A6CjAmJOWvA\&t=5s)
</Note>

## Docker Compose

This guide assumes that you have docker installed, with a reasonable amount of resources to run Postiz. This Docker Compose setup has been tested with;

* Virtual Machine, Ubuntu 24.04, 2Gb RAM, 2 vCPUs.

<Snippet file="installation-pre-reqs.mdx" />

### Configuration uses environment variables

The docker containers for Postiz are entirely configured with environment variables.

* **Option A** - environment variables in your `docker-compose.yml` file
* **Option B** - environment variables in a `postiz.env` file mounted in `/config` for the Postiz container only
* **Option C** - environment variables in a `.env` file next to your `docker-compose.yml` file (not recommended).

... or a mixture of the above options!

There is a [configuration reference](/configuration/reference) page with a list
of configuration settings.

## Example `docker-compose.yml` file

```yaml  theme={null}
services:
  postiz:
    image: ghcr.io/gitroomhq/postiz-app:latest
    container_name: postiz
    restart: always
    environment:
      # === Required Settings
      MAIN_URL: "https://postiz.your-server.com"
      FRONTEND_URL: "https://postiz.your-server.com"
      NEXT_PUBLIC_BACKEND_URL: "https://postiz.your-server.com/api"
      JWT_SECRET: "random string that is unique to every install - just type random characters here!"
      DATABASE_URL: "postgresql://postiz-user:postiz-password@postiz-postgres:5432/postiz-db-local"
      REDIS_URL: "redis://postiz-redis:6379"
      BACKEND_INTERNAL_URL: "http://localhost:3000"
      IS_GENERAL: "true"
      DISABLE_REGISTRATION: "false"

      # === Storage Settings
      STORAGE_PROVIDER: "local"
      UPLOAD_DIRECTORY: "/uploads"
      NEXT_PUBLIC_UPLOAD_DIRECTORY: "/uploads"

      # === Cloudflare (R2) Settings
      CLOUDFLARE_ACCOUNT_ID: "your-account-id"
      CLOUDFLARE_ACCESS_KEY: "your-access-key"
      CLOUDFLARE_SECRET_ACCESS_KEY: "your-secret-access-key"
      CLOUDFLARE_BUCKETNAME: "your-bucket-name"
      CLOUDFLARE_BUCKET_URL: "https://your-bucket-url.r2.cloudflarestorage.com/"
      CLOUDFLARE_REGION: "auto"

      # === Social Media API Settings
      X_API_KEY: ""
      X_API_SECRET: ""
      LINKEDIN_CLIENT_ID: ""
      LINKEDIN_CLIENT_SECRET: ""
      REDDIT_CLIENT_ID: ""
      REDDIT_CLIENT_SECRET: ""
      GITHUB_CLIENT_ID: ""
      GITHUB_CLIENT_SECRET: ""
      BEEHIIVE_API_KEY: ""
      BEEHIIVE_PUBLICATION_ID: ""
      THREADS_APP_ID: ""
      THREADS_APP_SECRET: ""
      FACEBOOK_APP_ID: ""
      FACEBOOK_APP_SECRET: ""
      YOUTUBE_CLIENT_ID: ""
      YOUTUBE_CLIENT_SECRET: ""
      TIKTOK_CLIENT_ID: ""
      TIKTOK_CLIENT_SECRET: ""
      PINTEREST_CLIENT_ID: ""
      PINTEREST_CLIENT_SECRET: ""
      DRIBBBLE_CLIENT_ID: ""
      DRIBBBLE_CLIENT_SECRET: ""
      DISCORD_CLIENT_ID: ""
      DISCORD_CLIENT_SECRET: ""
      DISCORD_BOT_TOKEN_ID: ""
      SLACK_ID: ""
      SLACK_SECRET: ""
      SLACK_SIGNING_SECRET: ""
      MASTODON_URL: "https://mastodon.social"
      MASTODON_CLIENT_ID: ""
      MASTODON_CLIENT_SECRET: ""

      # === OAuth & Authentik Settings
      NEXT_PUBLIC_POSTIZ_OAUTH_DISPLAY_NAME: "Authentik"
      NEXT_PUBLIC_POSTIZ_OAUTH_LOGO_URL: "https://raw.githubusercontent.com/walkxcode/dashboard-icons/master/png/authentik.png"
      POSTIZ_GENERIC_OAUTH: "false"
      POSTIZ_OAUTH_URL: "https://auth.example.com"
      POSTIZ_OAUTH_AUTH_URL: "https://auth.example.com/application/o/authorize"
      POSTIZ_OAUTH_TOKEN_URL: "https://auth.example.com/application/o/token"
      POSTIZ_OAUTH_USERINFO_URL: "https://authentik.example.com/application/o/userinfo"
      POSTIZ_OAUTH_CLIENT_ID: ""
      POSTIZ_OAUTH_CLIENT_SECRET: ""
      # POSTIZ_OAUTH_SCOPE: "openid profile email"  # Optional: uncomment to override default scope

      # === Misc Settings
      OPENAI_API_KEY: ""
      NEXT_PUBLIC_DISCORD_SUPPORT: ""
      NEXT_PUBLIC_POLOTNO: ""
      API_LIMIT: 30

      # === Payment / Stripe Settings
      FEE_AMOUNT: 0.05
      STRIPE_PUBLISHABLE_KEY: ""
      STRIPE_SECRET_KEY: ""
      STRIPE_SIGNING_KEY: ""
      STRIPE_SIGNING_KEY_CONNECT: ""

      # === Developer Settings
      NX_ADD_PLUGINS: false

      # === Short Link Service Settings (Optional - leave blank if unused)
      # DUB_TOKEN: ""
      # DUB_API_ENDPOINT: "https://api.dub.co"
      # DUB_SHORT_LINK_DOMAIN: "dub.sh"
      # SHORT_IO_SECRET_KEY: ""
      # KUTT_API_KEY: ""
      # KUTT_API_ENDPOINT: "https://kutt.it/api/v2"
      # KUTT_SHORT_LINK_DOMAIN: "kutt.it"
      # LINK_DRIP_API_KEY: ""
      # LINK_DRIP_API_ENDPOINT: "https://api.linkdrip.com/v1/"
      # LINK_DRIP_SHORT_LINK_DOMAIN: "dripl.ink"

    volumes:
      - postiz-config:/config/
      - postiz-uploads:/uploads/
    ports:
      - 5000:5000
    networks:
      - postiz-network
    depends_on:
      postiz-postgres:
        condition: service_healthy
      postiz-redis:
        condition: service_healthy

  postiz-postgres:
    image: postgres:17-alpine
    container_name: postiz-postgres
    restart: always
    environment:
      POSTGRES_PASSWORD: postiz-password
      POSTGRES_USER: postiz-user
      POSTGRES_DB: postiz-db-local
    volumes:
      - postgres-volume:/var/lib/postgresql/data
    networks:
      - postiz-network
    healthcheck:
      test: pg_isready -U postiz-user -d postiz-db-local
      interval: 10s
      timeout: 3s
      retries: 3
  postiz-redis:
    image: redis:7.2
    container_name: postiz-redis
    restart: always
    healthcheck:
      test: redis-cli ping
      interval: 10s
      timeout: 3s
      retries: 3
    volumes:
      - postiz-redis-data:/data
    networks:
      - postiz-network


volumes:
  postgres-volume:
    external: false

  postiz-redis-data:
    external: false

  postiz-config:
    external: false

  postiz-uploads:
    external: false

networks:
  postiz-network:
    external: false
```

## How to use docker compose

Save the file contents to `docker-compose.yml` in your directory you create for postiz.

Run `docker compose up` to start the services.

<Warning>
  **Note** When you change variables, you must run `docker compose down` and
  then `docker compose up` to recreate these containers with these updated
  variables.
</Warning>

Look through the logs for startup errors, and if you have problems, check out the [support](/support) page.

If everything looks good, then you can access the Postiz web interface at [https://postiz.your-server.com](https://postiz.your-server.com)

<Snippet file="docker-envvar-apps.mdx" />

## Next Steps

<CardGroup cols={2}>
  <Card title="Architecture" icon="diagram-project" href="/howitworks">
    Learn the architecture of the project
  </Card>

  <Card title="Providers" icon="plug" href="/providers/overview">
    Set up providers such as LinkedIn, X and Reddit
  </Card>
</CardGroup>


# Helm
Source: https://docs.postiz.com/installation/kubernetes-helm

Install Postiz using Kubernetes and Helm

<Snippet file="earlydoc.mdx" />

<Snippet file="installation-recommended-options.mdx" />

<Snippet file="installation-pre-reqs.mdx" />

## The Helm Chart

Postiz has a helm chart that is in very active development. You can find it here;

Note that this is a OCI compliant helm chart, meaning that you don't do `helm repo add`, and if you are using Flux or Helm, you must set them to OCI mode.

[https://github.com/gitroomhq/postiz-helmchart](https://github.com/gitroomhq/postiz-helmchart)

The `values.yml` file can be found in the repository, or a direct link to it is: [https://github.com/gitroomhq/postiz-helmchart/blob/main/charts/postiz/values.yaml](https://github.com/gitroomhq/postiz-helmchart/blob/main/charts/postiz/values.yaml)

## Next Steps

<CardGroup cols={2}>
  <Card title="Providers" icon="plug" href="/providers/overview">
    Set up providers such as LinkedIn, X and Reddit
  </Card>

  <Card title="Architecture" icon="diagram-project" href="/howitworks">
    Learn the architecture of the project
  </Card>
</CardGroup>


# Introduction
Source: https://docs.postiz.com/introduction

Welcome to Postiz documentation

<Note>
  YouTube Channel: [https://youtube.com/@postizofficial](https://youtube.com/@postizofficial)
</Note>

## What is Postiz?

Postiz helps you to manage all your social media accounts.

* Schedule social media and articles
* Generate posts with AI
* Exchange or buy posts from other members on the marketplace

<CardGroup cols={2}>
  <Card title="Quickstart" icon="rocket" href="/quickstart">
    Learn how to install the project and start using it
  </Card>

  <Card title="Architecture" icon="diagram-project" href="/howitworks">
    Learn the architecture of the project
  </Card>
</CardGroup>


# Bluesky
Source: https://docs.postiz.com/providers/bluesky

How to add Bluesky to Postiz

<Snippet file="never-share.mdx" />

<Steps>
  <Step title="Add BlueSky as a Channel">
    You do not need to configure any environment variables for BlueSky, you can simply add your account from the UI.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b8a7b6ecd0811bdc3bd29e594800693a" alt="New Channel" data-og-width="440" width="440" data-og-height="474" height="474" data-path="images/providers/bluesky/bluesky_add.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f5e5e2af8f1427ea697e32b89608709d 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3e2023177acb84302b32d717b5599c0b 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b144277cea7d048ae7c5d9dd9269fa45 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=cdb56aca823dc1f99422c63ddec70710 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=11384e20406d532ef207875d8085032a 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/bluesky/bluesky_add.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d3626803e6bc022a27b46e5db65a161e 2500w" />

    You should be redirected and be able to start posting immediately. If you have any issues, check the backend service logs.
  </Step>
</Steps>


# Discord
Source: https://docs.postiz.com/providers/discord

How to add Discord to your system

<Snippet file="never-share.mdx" />

<Note>
  This integration requires that you have **Manage Server** permissions on the Discord server you want to integrate with.
</Note>

<Steps>
  <Step title="Create a Discord Application">
    Login to Discord on the web, and then go to the [Discord Developer Portal](https://discord.com/developers/applications) and click on "New Application".

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a6ffbe766c52fa977fbb1471f822a622" alt="New Application" data-og-width="425" width="425" data-og-height="265" height="265" data-path="images/providers/discord/discord_create_app.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fb5a99726a41dd7a3e597c4853a8e8f6 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=9ddbe811429c9926532c9a24bf2010bf 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6f0c2138416ec0266cdaccb892cf4ec3 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=949e87a961700f30f33ef0a51009b6a6 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a5cda8ba8dad8e244ebbf9ea140cdb32 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_create_app.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8db668428a3fc2b6d9736bd5d0816e72 2500w" />
  </Step>

  <Step title="Add an App Icon">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=aa5d00c7f1888b7d11cfba9e9d6fc121" alt="App Icon" data-og-width="283" width="283" data-og-height="307" height="307" data-path="images/providers/discord/discord_add_app_icon.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5f40561b6e520c8b3be6259b1596e0d0 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3584ca5823f337a922ad82624bf59c80 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=52b1dec90f169ad78fb76f7e14c442a8 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3e0efd7fbdc539a6969bcd4f8f6fd81c 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=112ae5da1118c678415e159f692d3372 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_add_app_icon.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d536aa32fc10e80cb4a24e8a0462bbe2 2500w" />

    Upload the App Icon of your choice (1024x1024px max) and save your changes. If you do not do this, you will get 404 errors in logs when trying to add the Discord channel in the Postiz web interface.
  </Step>

  <Step title="Get and set your Client ID and Client Secret">
    You can find this in the **OAuth2** section of the Discord Developer Portal.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2e38f8146bffb0a1197781cdcc5925a2" alt="Copy Keys" data-og-width="833" width="833" data-og-height="336" height="336" data-path="images/providers/discord/discord_copy_keys.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fa274f19f49069f688132cc7b6e32ca8 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b1843fa323d22d9b998d1c58554ebd88 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b867c793757b057c025db533c5c0e49d 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=9f968efa866bf691eb7c244820074e9a 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=249d7554c35f2b379dfcadd8cc2ee22b 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_copy_keys.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1a408b8f45fbc28096bd56a59e84b508 2500w" />

    Set these in your .env file as follows;

    ```env  theme={null}
    DISCORD_CLIENT_ID="your_client_id"
    DISCORD_CLIENT_SECRET="your_client_secret"
    ```
  </Step>

  <Step title="Add a Redirect URI">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `discord` in the redirect URI.

    The redirect URI is the URL that Discord will redirect to after you have logged in. Assuming you are running Postiz on `postiz.example.com`, this would be: `https://postiz.example.com/integrations/social/discord`. Alternatively if you are running on `localhost:4200`, this would be `http://localhost:4200/integrations/social/discord`. You only really need one of these, depending on where you are running Postiz.

    You can find this in the **OAuth2** section of the Discord Developer Portal.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=20caf16e1d34d0344ad8e339d8b0ee34" alt="Redirect URI" data-og-width="474" width="474" data-og-height="283" height="283" data-path="images/providers/discord/discord_redirect_uri.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=13aa4d9ef19ecf9b3e0adac3ae5249c7 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fd883f25d1099ecca07da1de097cff3f 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=52e3ee08242dfe636ccfcc137ea5dbea 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=23e7ddc0590738861658b576e1fd667e 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0a759f6a8c9e57ab3fac1297213a3843 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/discord/discord_redirect_uri.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=77839c69c85adb6cf8abda7dcef2e004 2500w" />
  </Step>

  <Step title="Create a Bot">
    Navigate to the "Bot" section of the Discord Developer Portal. Fill out the bot details however you like, and then click "Reset Token".

    With the token that is generated, set it in your .env file as follows;

    ```env  theme={null}
    DISCORD_BOT_TOKEN_ID="your_bot_token"
    ```

    If you do not set this, you will get 404 errors when trying to add the Discord channel in the Postiz web interface.

    Stop Postiz if it is running, and then start it using the .env file with the Discord details.
  </Step>

  <Step title="Add a Discord channel in the Postiz web interface">
    Go to the Postiz web interface, and click on the "Add Channel" button, and then select "Discord". You should be redirected to Discord to login.
  </Step>
</Steps>


# Dribbble
Source: https://docs.postiz.com/providers/dribbble

How to add Dribbble to your system

<Snippet file="never-share.mdx" />

<Steps>
  <Step title="Register your application">
    [Register your application on Dribbble](https://dribbble.com/account/applications/new).

    * **Name:** `MyPostizInstance`
    * **Description:** `My Postiz Instance`
    * **Website:** `https://example.com`
    * **Redirect URI:** (see below)

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `dribble` in the redirect URI.
  </Step>

  <Step title="Copy your client secret to environment variables">
    These can be found immediately after registering your application. These are both 64 characters long.

    ```env  theme={null}
    DRIBBLE_CLIENT_ID="1234..."
    DRIBBLE_CLIENT_SECRET="5678..."
    ```

    Restart Postiz with the updated environment variables
  </Step>

  <Step title="Add a Dribbble channel in the Postiz web interface">
    Go to the Postiz web interface, and click on the "Add Channel" button. Select "Dribbble" from the list of available channels. You should be redirected to Dribbble to authorize the application.
  </Step>
</Steps>


# Facebook
Source: https://docs.postiz.com/providers/facebook

How to add Facebook to your system

<Snippet file="never-share.mdx" />

<Warning>
  **NOTE:** Please be advised that Instagram and Facebook can use the same app (no need to create two separate apps)
</Warning>

<Steps>
  <Step title="Create a new app">
    Select a business portfolio, then create a [new app in Facebook developers](https://developers.facebook.com/apps/creation/).

    Please be advised that for public applications, you will need to verify your business.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0c8b3ff1674149ae9a9a312b614a4d4f" alt="Business Portfolio" data-og-width="1854" width="1854" data-og-height="511" height="511" data-path="images/providers/facebook/Business-Portfolio.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=803624d01a29c97332020adc2cd23fa9 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3a6b394b4392b42e33857d4853548893 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=15da50080ff42a807fab58798e5b3d02 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b1114a78681df12e01ca3701e7121fb0 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=73af298e5ffb31f66160a52059b69b85 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0050038f66bb0b9e35588dfecfbde314 2500w" />

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ddddf127593b97f363d10b6cdffb954a" alt="Create app" data-og-width="1332" width="1332" data-og-height="555" height="555" data-path="images/providers/facebook/Create-an-app.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5bc6ebb9f213c6baef0a383d9dc013a2 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f1827da314ef645fe9044408860e4f95 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b14a6b6fe1021ca767d70ffaee9525b6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=e6a8ac9dd3db5a08c24e4c7dab6e9651 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b0e9ca5407419265d661c42b18bf809a 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=57ff99f3b14634fd7a91e604e2b6139b 2500w" />
  </Step>

  <Step title="Select app type">
    Select "Other" and click next

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b3019dc009d89b072f2c1115a980bbba" alt="Other app use cases" data-og-width="1074" width="1074" data-og-height="996" height="996" data-path="images/providers/facebook/Other-app-use-cases.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ee90868bf3a0909bcd0928100c5c62a6 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=517657ad0c93c36f30fce65719a461bc 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a390e75175f369fdc6d9b672a6a450a6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a2b3998cacd29047d31b0bed3cdc8833 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5fcc7c7e4c582405840a3346597b6923 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=576f9cf0569ed5f5cfaea270953a97f6 2500w" />
  </Step>

  <Step title="Select business">
    Then select business

    ![Business](https://github.com/user-attachments/assets/74bde861-5441-46bb-b5b8-c5229e980237)
  </Step>

  <Step title="Add details and create app">
    Add all your details and click Create App

    ![Create app details](https://github.com/user-attachments/assets/f0c03825-0f9b-4467-94a4-ab8cf6ed7e1d)
  </Step>

  <Step title="Set up Login with Facebook">
    ![Setup Login with Facebook](https://github.com/user-attachments/assets/08d3c1d1-d498-49d1-adac-aa6248e7c10c)

    Set up login for business
  </Step>

  <Step title="Configure redirect URI">
    Set up a redirect URI back to the application

    ![step 6](https://github.com/user-attachments/assets/8bf1774b-b6fe-4ac6-aea5-97d8c8bbf5da)

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `facebook` in the redirect URI.
  </Step>

  <Step title="Request advanced permissions">
    ![step 7](https://github.com/user-attachments/assets/a81aa2a3-de66-4099-906b-b78c641d1a23)

    Go to advanced permission and request access for the following scopes:

    * `pages_show_list`
    * `business_management`
    * `pages_manage_posts`
    * `pages_manage_engagement`
    * `pages_read_engagement`
    * `read_insights`

    <Note>
      If your Postiz install is for personal use only these advanced permissions are not required for Postiz to function.
    </Note>
  </Step>

  <Step title="Set app mode to Live">
    Change the App Mode from 'Development' to 'Live'. If you do not do this then posts made via the API will display for yourself but will not be visible for other users.
  </Step>

  <Step title="Copy your credentials">
    ![Keys](https://github.com/user-attachments/assets/ac11f87f-4951-47f8-8344-7fbc9de942e4)

    Go to basic permissions copy your App ID and App Secret and paste them in your `.env` file

    ```env  theme={null}
    FACEBOOK_APP_ID="app id"
    FACEBOOK_APP_SECRET="app secret"
    ```

    Facebook should now be working!
  </Step>
</Steps>


# Instagram
Source: https://docs.postiz.com/providers/instagram

How to add Instagram to your system

<Snippet file="never-share.mdx" />

<Warning>
  **NOTE:** Please be advised that Instagram and Facebook can use the same app (no need to create two separate apps)
</Warning>

## Connection Options

There are two ways to connect to an Instagram account: by using a Facebook Business or through a standalone flow that connects directly to an Instagram account. Both methods will require a [Meta for Developers account](https://developers.facebook.com/apps/).

## Setting up Meta Application

The following steps will guide you through the setup of a Meta application that can be used for connecting Instagram to Postiz.

<Steps>
  <Step title="Create a new app">
    Select a business portfolio, then create a [new app in Meta for developers](https://developers.facebook.com/apps/creation/).

    Please be advised that for public applications, you will need to verify your business.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0c8b3ff1674149ae9a9a312b614a4d4f" alt="Business Portfolio" data-og-width="1854" width="1854" data-og-height="511" height="511" data-path="images/providers/facebook/Business-Portfolio.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=803624d01a29c97332020adc2cd23fa9 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3a6b394b4392b42e33857d4853548893 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=15da50080ff42a807fab58798e5b3d02 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b1114a78681df12e01ca3701e7121fb0 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=73af298e5ffb31f66160a52059b69b85 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Business-Portfolio.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0050038f66bb0b9e35588dfecfbde314 2500w" />

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ddddf127593b97f363d10b6cdffb954a" alt="Create app" data-og-width="1332" width="1332" data-og-height="555" height="555" data-path="images/providers/facebook/Create-an-app.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5bc6ebb9f213c6baef0a383d9dc013a2 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f1827da314ef645fe9044408860e4f95 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b14a6b6fe1021ca767d70ffaee9525b6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=e6a8ac9dd3db5a08c24e4c7dab6e9651 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b0e9ca5407419265d661c42b18bf809a 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Create-an-app.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=57ff99f3b14634fd7a91e604e2b6139b 2500w" />
  </Step>

  <Step title="Select app type">
    Select "Other" and click next

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b3019dc009d89b072f2c1115a980bbba" alt="Other app use cases" data-og-width="1074" width="1074" data-og-height="996" height="996" data-path="images/providers/facebook/Other-app-use-cases.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ee90868bf3a0909bcd0928100c5c62a6 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=517657ad0c93c36f30fce65719a461bc 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a390e75175f369fdc6d9b672a6a450a6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a2b3998cacd29047d31b0bed3cdc8833 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5fcc7c7e4c582405840a3346597b6923 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/facebook/Other-app-use-cases.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=576f9cf0569ed5f5cfaea270953a97f6 2500w" />
  </Step>

  <Step title="Select business">
    Then select business

    ![Business](https://github.com/user-attachments/assets/74bde861-5441-46bb-b5b8-c5229e980237)
  </Step>

  <Step title="Add details and create app">
    Add all your details and click Create App

    ![Create an app details page](https://github.com/user-attachments/assets/f0c03825-0f9b-4467-94a4-ab8cf6ed7e1d)
  </Step>
</Steps>

## Facebook Business Option

If you have a Facebook Business page that is linked to your Instagram account, you can connect to it by setting up the Login for Business flow.

<Steps>
  <Step title="Set up Login for Business">
    ![Setup Login with Instagram](https://github.com/user-attachments/assets/08d3c1d1-d498-49d1-adac-aa6248e7c10c)

    Set up login for business
  </Step>

  <Step title="Set up Redirect URI">
    Set up a redirect URI back to the application

    ![Instagram](https://github.com/user-attachments/assets/78496d3f-3b84-4724-afc8-ed217d892c6d)

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `instagram` in the redirect URI.
  </Step>

  <Step title="Set up permissions">
    ![step 7](https://github.com/user-attachments/assets/a81aa2a3-de66-4099-906b-b78c641d1a23)

    Go to advanced permission and request access for the following scopes:

    * `instagram_basic`
    * `pages_show_list`
    * `pages_read_engagement`
    * `business_management`
    * `instagram_content_publish`
    * `instagram_manage_comments`
    * `instagram_manage_insights`
  </Step>

  <Step title="Copy your credentials">
    ![Keys](https://github.com/user-attachments/assets/ac11f87f-4951-47f8-8344-7fbc9de942e4)

    Go to basic permissions copy your App ID and App Secret and paste them in your `.env` file

    ```env  theme={null}
    FACEBOOK_APP_ID="app id"
    FACEBOOK_APP_SECRET="app secret"
    ```

    Instagram should now be working!
  </Step>
</Steps>

## Instagram Standalone Option

If you want to connect directly to your Instagram account without having to use a Facebook Business, use the standalone option.

<Warning>
  Please note that standalone option requires a professional Instagram account.
</Warning>

<Steps>
  <Step title="Set up Instagram">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b8100a55eacb822dfa77c1b736075101" alt="&#x22;Add products to your app&#x22; section of app creation" data-og-width="3205" width="3205" data-og-height="1810" height="1810" data-path="images/providers/instagram-standalone/instagram-standalone-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=66bab919856dff30583a6c989ee3e771 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=cf2f0f57d87c12eb82100c308740dda4 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4ccdb53ff70ed13f5c4eed5f365c19b7 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7ffb8acc8293c40f44b7ea56b3411be3 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b0e965102ff00066273cebf22cd9d33b 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=566ed33cd8f4d2704cab4cd0fb932ba5 2500w" />

    Set up Instagram.
  </Step>

  <Step title="Set up Instagram Business Login">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fec54922d8021cc9c9751f708e4c369d" alt="Set up Instagram Business Login" data-og-width="2336" width="2336" data-og-height="734" height="734" data-path="images/providers/instagram-standalone/instagram-standalone-002.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=c5d66a4a1223948877354966800ede0b 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=93b5eb885305f7efc2233fcdc431c2dd 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=59821a4b6aab8f9eda687424e4520893 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7e033dc5ff6cefd453a8b38b4f6dad09 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ee0312337880de6d5276f39f3b4c4d25 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-002.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=40ae5e40ff5b86841c06b12a15e09285 2500w" />

    Click on the button to set up Instagram Business Login
  </Step>

  <Step title="Set up Redirect URI">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=bf0fbe883e3efb30b56c51fe23ffeaae" alt="Set up redirect URI" data-og-width="2511" width="2511" data-og-height="1756" height="1756" data-path="images/providers/instagram-standalone/instagram-standalone-003.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=e0bdb91845c1be02c65c6ec6c1b20132 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=12ae3664e0a8a618fb7928a89125e911 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=922abc04ee6ebd915e45016c68126540 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6ca86fb2e6e8167807fef142afe30010 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d04c22c65477e2b3e92223360d4bc2dc 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-003.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8c652b00d2801f1569f27ec68f67dbf5 2500w" />

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `instagram-standalone` in the redirect URI.
  </Step>

  <Step title="Copy Instagram App ID and Secret">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=c27df5eebc64cd407a1e9ab73d2e19bc" alt="Instagram App ID and Secret" data-og-width="2999" width="2999" data-og-height="1565" height="1565" data-path="images/providers/instagram-standalone/instagram-standalone-004.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1d9a6baf8ec345c1eded68078a143fa9 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ce266d16ee37673aa7d3e158ac76b861 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d776f59db05e8f119cb01f244a1f037f 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=dc95dec9e224d80092bc0b0a16738a95 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=05131f5e2684a4c8fb4a541913d01601 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/instagram-standalone-004.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=19886ebc89ecb8ce78f444bb54427bdb 2500w" />

    From your Instagram API setup screen copy the Instagram App ID and Instagram App Secret and paste them in your `.env` file

    ```env  theme={null}
    INSTAGRAM_APP_ID="app id"
    INSTAGRAM_APP_SECRET="app secret"
    ```
  </Step>

  <Step title="Add Instagram Standalone channel in Postiz Application">
    Go to the Postiz web interface, and click on the "Add Channel" button. Select "Instagram (Standalone)" from the list of available channels. You should be redirected to the Instagram login screen to authorize the application.
  </Step>
</Steps>

## Adding App Roles

If you're having trouble connecting to your Instagram accounts, adding them as App Roles may help.

<Steps>
  <Step title="Go to the App Roles page">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8faef5ba6e05f860bbb69ff2c722f7af" alt="Facebook App developers dashboard" data-og-width="1892" width="1892" data-og-height="860" height="860" data-path="images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=318c777356453d33205d68901a935825 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d93341ab47a2f1fd1c5b04cdab196efa 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d69c041545a96d760678f1904797bca8 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=58dd460fee5627efeb425d9692c37f53 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1234d1a7aee8e05e1b2aec39ff7b3ad8 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Facebook-App-developpers-dashboard.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=429ab6a2cd965a6f5b4539477fc826d1 2500w" />
  </Step>

  <Step title="Add a role">
    Click on "Add People"

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7a4d33181647a545aae6ff5ad05e9614" alt="App Roles page" data-og-width="1760" width="1760" data-og-height="321" height="321" data-path="images/providers/instagram-standalone/App-Roles-page.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d84dbb9294ef7c90461eb301d57ce5c1 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=18f8fdda67fb3a1f442927001dc186df 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=54eb4cc0f072aa9e7fe79f9e74916866 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2542137e223159b8f8fe3f2b0431905b 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=c8e84bcffb0a400510cde8c58a3251c3 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/App-Roles-page.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=dd8440c4ae24d24d28b2a6ab946f24f1 2500w" />
  </Step>

  <Step title="Add an Instagram Tester">
    Select the "Instagram Tester" option, and type in the handles of all the Instagram accounts you'd like to connect to. Then, click "Add".

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=12e8223cb9062959731f289bfdecd881" alt="Add people window" data-og-width="759" width="759" data-og-height="807" height="807" data-path="images/providers/instagram-standalone/Add-people-window.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=bb74a63c6cf09f4d84b274cc9310fafd 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=35e0ffe75fbaf605df3d2eba85165fc1 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8d887e2e8b48c43cba041907becb1e5a 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=444ef2b0b05692e481420c0cb138d080 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=122e7307074345f7e364adb4b4337b41 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Add-people-window.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5292da5dc820605a691db0335557f683 2500w" />
  </Step>

  <Step title="Accept the invitations">
    Go to your Instagram account, and accept the tester invitation in the [Apps and Websites section of the profile settings](https://www.instagram.com/accounts/manage_access/).

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=98a30e2d90d83a9afa2414ca1e4522f9" alt="Apps and Websites section of the profile settings" data-og-width="1406" width="1406" data-og-height="575" height="575" data-path="images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=04a48ceac8e0880cd3e6e126680f6993 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3b322416e288144fb93e5c8f09c527ef 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d1ef4179f1c7399d7da2f9be8ed220a8 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5931063c0d0241d1df7f6210e4877c21 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6d7bc95abbf2d50416e0476a6869a375 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/instagram-standalone/Apps-and-Websites-section-of-the-profile-settings.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0542366bede40c1cf87def2522011d30 2500w" />
  </Step>
</Steps>


# LinkedIn
Source: https://docs.postiz.com/providers/linkedin

How to add LinkedIn to your system

<Snippet file="never-share.mdx" />

<Steps>
  <Step title="Create a new app">
    Head over to [LinkedIn developers](https://www.linkedin.com/developers/apps) and create a new app.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=16897bbb984f26f014317375c2efd1f0" alt="LinkedIn" data-og-width="2366" width="2366" data-og-height="676" height="676" data-path="images/providers/linkedin/linkedin-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=29a8bcfbd879c47388ec75ae914ed1e0 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=309b2420a2044fe75f46b2dbfeed1f31 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=56323e45fa34e79762866839b7c21fc6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=01dcf080060a3f3a0dccd6e13267b04d 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ce25e4fdf89d3ebf81a35e5a17327806 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a8418bed9fb67b91f385c3004495e7fb 2500w" />
  </Step>

  <Step title="Add required products">
    Fill in all the details, once created head over to Products and make sure you add all the required products.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d61fd603ae5411068cebb08ebf37700e" alt="LinkedIn" data-og-width="2436" width="2436" data-og-height="1356" height="1356" data-path="images/providers/linkedin/linkedin-002.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5226661a61af71bc9cf4ab036ac57829 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=db7519aa8a2284218b596de06573beb5 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8ba70b360651124edd0a3c67f381f46e 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fd8ffb75457f735feaa39b8d39c25229 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3f247eacf663e2fb197e82104ba72050 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-002.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0a63cda111b0995255b0a12ac40d06f1 2500w" />

    <Warning>
      It is important to request the Advertising API permissions and fill up the request form, or you will not have the ability to refresh your tokens.
    </Warning>
  </Step>

  <Step title="Configure OAuth2 Redirect URI">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `linkedin` in the redirect URI.

    <Note>
      If you are using the "LinkedIn Page" provider, then the redirect url should be "linkedin-page".
    </Note>
  </Step>

  <Step title="Copy your credentials">
    Copy the created `Client ID` and `Client Secret` and add them to your `.env` file.

    ```env  theme={null}
    LINKEDIN_CLIENT_ID=""
    LINKEDIN_CLIENT_SECRET=""
    ```

    You can find those under the Auth Tab of your LinkedIn App in the developer portal.
  </Step>
</Steps>


# LinkedIn Page
Source: https://docs.postiz.com/providers/linkedin-page

How to add a LinkedIn Page to your system

<Snippet file="never-share.mdx" />

<Steps>
  <Step title="Create a new app">
    Head over to [LinkedIn developers](https://www.linkedin.com/developers/apps) and create a new app.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=16897bbb984f26f014317375c2efd1f0" alt="LinkedIn" data-og-width="2366" width="2366" data-og-height="676" height="676" data-path="images/providers/linkedin/linkedin-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=29a8bcfbd879c47388ec75ae914ed1e0 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=309b2420a2044fe75f46b2dbfeed1f31 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=56323e45fa34e79762866839b7c21fc6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=01dcf080060a3f3a0dccd6e13267b04d 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ce25e4fdf89d3ebf81a35e5a17327806 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a8418bed9fb67b91f385c3004495e7fb 2500w" />
  </Step>

  <Step title="Verify your app">
    Verify your app with LinkedIn

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8f60b96da521585d8e0f09a95b969d17" alt="LinkedIn" data-og-width="739" width="739" data-og-height="281" height="281" data-path="images/providers/linkedin/linkedin-004.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5c942cacc15fb09741bb0072c6e570c3 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=9025270c7b4470990c5571bbd757571e 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=def1c8fdea5b84d75bb0c7733189e705 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=06a16c8d477ec9982c51209e67083f2c 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=eaa4971663d1f3475317adea8e46c751 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/linkedin/linkedin-004.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2128e4b3e3d6619d64b2a54e604162f1 2500w" />

    You will need to follow the verification process to request the necessary permissions listed below.
  </Step>

  <Step title="Add required products">
    Fill in all the details, once created head over to Products and make sure you add all the required products:

    * Share on LinkedIn
    * Advertising API
    * Sign in with LinkedIn using OpenID connect

    <Warning>
      It is important to request the Advertising API permissions and fill up the request form, or you will not have the ability to refresh your tokens.
    </Warning>
  </Step>

  <Step title="Configure OAuth2 Redirect URI">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `linkedin-page` in the redirect URI.
  </Step>

  <Step title="Copy your credentials">
    Copy the created `Client ID` and `Client Secret` and add them to your `.env` file.

    ```env  theme={null}
    LINKEDIN_CLIENT_ID=""
    LINKEDIN_CLIENT_SECRET=""
    ```

    You can find those under the Auth Tab of your LinkedIn App in the developer portal.
  </Step>
</Steps>


# Mastodon
Source: https://docs.postiz.com/providers/mastodon

How to add Mastodon to your system

<Snippet file="never-share.mdx" />

<Note>
  Watch the YouTube Tutorial: [https://youtu.be/IAnfbE\_htqg?si=z30m5qS8qLDN9R0X](https://youtu.be/IAnfbE_htqg?si=z30m5qS8qLDN9R0X)
</Note>

Mastodon client registration is not done via the web interface, but by talking to the API directly. In the example below, we use `curl` to register a new client.

Optionally check that you have `jq` installed on your system. You can normally install this with brew, apt-get, yum or chocolatey. If you don't have `jq` installed, you can remove it from the command below.

<Info>
  The examples on this page use `https://mastodon.social` as the default Mastodon instance. If you are setting up Postiz to connect to a different self-hosted Mastodon instance (e.g., `https://fosstodon.org`), you must replace `https://mastodon.social` with your instance's URL in the `curl` command below. You will also need to ensure the `MASTODON_URL` environment variable in your application's `.env` file (or equivalent configuration for Docker, etc.) is set to your custom instance's URL.
</Info>

<Steps>
  <Step title="Register your client">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `mastodon` in the redirect URI.

    Run the following curl command in a terminal to get the Mastodon client id and client secret.

    ```bash  theme={null}
    curl -X POST -sS https://mastodon.social/api/v1/apps -F "client_name=YourAppName" -F "redirect_uris=http://localhost:4200/integrations/social/mastodon" -F "scopes=write:statuses write:media profile" | jq
    ```

    This will give you output that looks something like this;

    ```json  theme={null}
    {
      "id": "1234567890",
      "redirect_uris": [
        "http://localhost:4200/integrations/social/mastodon"
      ],
      ...
      "client_id": "your_client_id",
      "client_secret": "your_client_secret"
    }
    ```
  </Step>

  <Step title="Add credentials to your environment">
    Make a note of your `client_id` and `client_secret` and add them to your `.env` file.

    ```env  theme={null}
    MASTODON_CLIENT_ID="shown in the output from the above command"
    MASTODON_CLIENT_SECRET="shown in the output from the above command"
    MASTODON_URL="https://mastodon.social" # Change this if connecting to a different instance
    ```
  </Step>

  <Step title="Start Postiz">
    Stop Postiz if it is running, and then start it using the .env file with the Mastodon details. Click through the new channel setup and you should be asked to login on Mastodon.
  </Step>
</Steps>


# Providers Overview
Source: https://docs.postiz.com/providers/overview

Configure social media providers for Postiz

You can see all the providers that Postiz supports documented in the sidebar, under "**Providers**".

Please note that no providers are configured by default. You will need to configure them all in your `.env` file, or as environment variables. You will need to restart Postiz whenever you change environment variables. If you are using docker compose, you must run `docker compose down` and then `docker compose up` to rebuild the containers with the updated variables.

<Snippet file="never-share.mdx" />

## Available Providers

<CardGroup cols={3}>
  <Card title="X (Twitter)" icon="x-twitter" href="/providers/x-twitter">
    Post to X/Twitter
  </Card>

  <Card title="LinkedIn" icon="linkedin" href="/providers/linkedin">
    Post to LinkedIn profiles
  </Card>

  <Card title="LinkedIn Page" icon="linkedin" href="/providers/linkedin-page">
    Post to LinkedIn pages
  </Card>

  <Card title="Facebook" icon="facebook" href="/providers/facebook">
    Post to Facebook pages
  </Card>

  <Card title="Instagram" icon="instagram" href="/providers/instagram">
    Post to Instagram
  </Card>

  <Card title="Threads" icon="threads" href="/providers/threads">
    Post to Threads
  </Card>

  <Card title="Bluesky" icon="cloud" href="/providers/bluesky">
    Post to Bluesky
  </Card>

  <Card title="Mastodon" icon="mastodon" href="/providers/mastodon">
    Post to Mastodon
  </Card>

  <Card title="YouTube" icon="youtube" href="/providers/youtube">
    Post to YouTube
  </Card>

  <Card title="TikTok" icon="tiktok" href="/providers/tiktok">
    Post to TikTok
  </Card>

  <Card title="Reddit" icon="reddit" href="/providers/reddit">
    Post to Reddit
  </Card>

  <Card title="Pinterest" icon="pinterest" href="/providers/pinterest">
    Post to Pinterest
  </Card>

  <Card title="Discord" icon="discord" href="/providers/discord">
    Post to Discord
  </Card>

  <Card title="Slack" icon="slack" href="/providers/slack">
    Post to Slack
  </Card>

  <Card title="Telegram" icon="telegram" href="/providers/telegram">
    Post to Telegram
  </Card>

  <Card title="Dribbble" icon="dribbble" href="/providers/dribbble">
    Post to Dribbble
  </Card>
</CardGroup>


# Pinterest
Source: https://docs.postiz.com/providers/pinterest

How to add Pinterest to your system

<Snippet file="never-share.mdx" />

<Note>
  This integration requires that you have a Pinterest Company Account.
</Note>

<Steps>
  <Step title="Create Pinterest App">
    Head to [Pinterest Developer Dashboard](https://developers.pinterest.com/apps/) and create your App. Fill out all required Information and wait on the App to get approved.
  </Step>

  <Step title="Copy the App ID and Secret">
    Copy the App ID at "App id" and the Secret Key at "App secret key"

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2ec054ee906e8759a9cdc6d41de81a10" alt="Copy App ID and Secret" data-og-width="814" width="814" data-og-height="183" height="183" data-path="images/providers/pinterest/pinterest-002.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=cf2906888bfb9dd18251e148d7a1294e 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=e77ac4efb0185930c3ce046087eaf231 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1a336c3c88136a38c6815fe1e9822fa5 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3abd6ce28f9ac5e7394fa11c81ad9723 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b9f6c125e3b5609381b7b8264b23b5f6 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-002.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f370767727691f87fe148beea01b35c3 2500w" />
  </Step>

  <Step title="Configure Redirect URI">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `pinterest` in the redirect URI.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=63895562c88b0d244179ba28647cdf2f" alt="Setup of Redirect URIs" data-og-width="817" width="817" data-og-height="319" height="319" data-path="images/providers/pinterest/pinterest-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7c878dad2281746f5c8b2b49a86223fb 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ee149a628137211d0f897410f0cc48e8 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f4b12eba2db9339094764774561d10c6 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=138da9576b72d3c1ebc254ff8fe95c49 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b8cdf6d552fe56f5ee511991bf131af8 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/pinterest/pinterest-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=19b0774700de8f43ccb0a3561395fa43 2500w" />
  </Step>

  <Step title="Add environment variables">
    ```env  theme={null}
    PINTEREST_CLIENT_ID=""
    PINTEREST_CLIENT_SECRET=""
    ```

    You should now be able to add the Pinterest Provider to your User / Team Account.
  </Step>
</Steps>


# Reddit
Source: https://docs.postiz.com/providers/reddit

How to add Reddit to your system

<Snippet file="never-share.mdx" />

<Steps>
  <Step title="Create an app on Reddit Developers">
    Head over to [Reddit developers](https://www.reddit.com/prefs/apps) and click on **create a new app**.

    * **Name:** `MyPostizInstance` (or whatever you like)
    * **App type:** `web app`
    * **Redirect URI:** (see below)

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `reddit` in the redirect URI.
  </Step>

  <Step title="Set environment variables">
    Copy the Reddit client id and client secret and add them to your `.env` file.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4da3950d7359a08ae23aca3aba064816" alt="Reddit" data-og-width="1826" width="1826" data-og-height="742" height="742" data-path="images/providers/reddit/reddit-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d9e462570abc3049e558309eecb48bb2 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=82491cfef9e894b575a95769a11b70dc 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=416eff134cbc23787ab9c33353264f4c 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1be78f74f78f425ee2dfbe7de0efcc19 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=12fba7cf2615ef6c8a314882fbb79648 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/reddit/reddit-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=52fba4b1c15a3e5275dace05c9026887 2500w" />

    ```env  theme={null}
    REDDIT_CLIENT_ID=""
    REDDIT_CLIENT_SECRET=""
    ```
  </Step>
</Steps>


# Slack
Source: https://docs.postiz.com/providers/slack

How to add Slack to your system

<Snippet file="never-share.mdx" />

<Note>
  This integration requires that you have a Slack Workspace
</Note>

<Steps>
  <Step title="Create Slack App">
    Head to [Slack Applications Dashboard](https://api.slack.com/apps) and select "From scratch", fill out all the required Information.
  </Step>

  <Step title="Set up OAuth Scopes and redirect URIs">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `slack` in the redirect URI.

    Head to Features > OAuth & Permissions

    1. Add the redirect URI (see above)
    2. At Scopes > Bot Token Scopes, add these Scopes:
       * `chat:write`
       * `channels:read`
       * `users:read`
       * `groups:read`
       * `channels:join`
  </Step>

  <Step title="Set an App Icon">
    Head back to Settings > Basic Information > Display Information

    Now set an Icon that meets these Requirements:

    1. It has to be a Square
    2. It has to be 512px×512px to 2000px×2000px

    If you do not set an App Icon, Postiz won't let you install the Integration.
  </Step>

  <Step title="Copy Client ID and Secret">
    Head back to App Credentials, copy the Client ID and Secret and paste it to your Environment:

    ```env  theme={null}
    SLACK_ID=""
    SLACK_SECRET=""
    ```
  </Step>
</Steps>


# Telegram
Source: https://docs.postiz.com/providers/telegram

How to add Telegram to your system

<Snippet file="never-share.mdx" />

<Steps>
  <Step title="Create a Telegram Bot">
    1. Open Telegram and message [@BotFather](https://t.me/botfather).
    2. Click Start
    3. Click Menu
       * Click "Create a new bot"
       * Enter a name for your bot (e.g., `MyPostizBot`).

    ![Name Bot](https://github.com/user-attachments/assets/974c1ad3-4648-4d41-8d20-22b6e8cb0bc8)

    * Choose a unique username ending with "bot" (e.g., `MyPostizBot_bot`).

    ![Unique Bot Name](https://github.com/user-attachments/assets/e4824ed2-c812-4a03-b43a-bf4f680eff23)

    Once your bot is created, **BotFather** will give you an **API Token**. Keep it safe—you'll need it later.

    ![Bot Token](https://github.com/user-attachments/assets/71bdc32c-36d5-45cd-b0d8-08d5eefcaadc)
  </Step>

  <Step title="Configure Bot Group Permissions (optional but recommended)">
    1. Click on "Menu"
    2. Click on "Edit your bots"
    3. Select your bot
    4. Click on "Bot Settings"

    ![Bot Settings](https://github.com/user-attachments/assets/908924ee-2567-4c13-bfeb-52f5684680a3)

    5. Click on "Group Privacy"

    ![Group Privacy](https://github.com/user-attachments/assets/6a756212-7af3-437f-88fe-74ca5579666d)

    6. If "Privacy mode" is enabled, turn it off (it is enabled by default)
  </Step>

  <Step title="Add Your Bot to Telegram Groups or Channels">
    1. Navigate to your group/channel
    2. Add your bot to your group/channel
    3. The bot requires these permissions to work with Postiz:
       * access to messages — so the bot can read messages sent in the group/channel
       * Send Text Messages — so the bot can send messages
       * Send Media — so the bot can send media

    <Info>
      While not strictly required, making your bot an `admin` is **recommended**. It will give the bot all the permissions needed and make the setup easier and faster.
    </Info>
  </Step>

  <Step title="Add Bot Token to Your Application">
    In your `.env` file, add the **Telegram Bot Name** (Without the @) and the **Telegram Bot API Token** that you received from **BotFather** in Step 1:

    ```env  theme={null}
    TELEGRAM_BOT_NAME="MyPostizBot_bot"
    TELEGRAM_TOKEN="MyPostizBot token"
    ```

    <Info>
      If you are using Docker Compose, include the `NTBA_FIX_350: 1` variable directly in your `docker-compose.yml`.
      See [Node Telegram Bot API](https://github.com/yagop/node-telegram-bot-api/blob/master/doc/usage.md#file-options-metadata) for reference.
    </Info>

    You should be able to connect your group/channel to Postiz now!
  </Step>
</Steps>


# Threads
Source: https://docs.postiz.com/providers/threads

How to add Threads to your system

<Snippet file="never-share.mdx" />

<Note>
  This integration requires that you have setup a Meta for Developers account already. You can start by going to the [Meta/Facebook Developer Portal](https://developers.facebook.com/apps).

  This is a complex integration, and it may take some time to get it right. If you have any issues, please reach out to us on the Postiz Discord.
</Note>

<Steps>
  <Step title="Create an app">
    Go to Meta for Developers and [create a new app](https://developers.facebook.com/apps/creation/). Give your app a name and email.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=daf64c01485d3cf25f6006b911a70bf3" alt="Create app" data-og-width="659" width="659" data-og-height="421" height="421" data-path="images/providers/threads/threads_app_name.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=e722206d4e3677396f0cf4eaa80d22e0 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8c74af49ff26f96ceeea7dbbf6092ccf 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=cadfcae40b2334b1b1dc0b9225a627ce 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0af56a556a416d079ac8f2444ab83886 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f71eaf3ad5e0513fe195ebbdb90322f9 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_app_name.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=263c39eab00e322446d63a2388b449c5 2500w" />
  </Step>

  <Step title="Request access to Threads">
    Select *Access the Threads API*.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=782d7249c2595e33a7a65c06865725c9" alt="Request access" data-og-width="992" width="992" data-og-height="498" height="498" data-path="images/providers/threads/threads_request_api.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a516a2ae8513200dc8b507f7b14f8cdc 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=081191d1afba8e5a5a68a39875c48736 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=368eb205e2996b97c54c4edb79a62596 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a50c1ae0f28509f482a78b6ef541bb39 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=074d259aa0930b3a252b0d4f870ab928 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_request_api.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=235421eba85198d4caa9cbd66bcf3366 2500w" />
  </Step>

  <Step title="Add business details, or skip">
        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=236cc81a67ee677870a627ec54970ea4" alt="Add business details" data-og-width="743" width="743" data-og-height="337" height="337" data-path="images/providers/threads/threads_business.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=bbac201f8f2f026e7368a762d41b90c0 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=dcaa40336096da84002bf57ed36a5ce7 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3b4d2fc5302b547a2fb64329d5958251 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b4a828c7530f30bdc6457ec2df4167b6 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b3b75691665a92e5f91e9916a5da539a 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_business.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b0ac83490194c9096ab81ea14d589b42 2500w" />

    If you're unable to skip or move to the next step, and get a message saying "There are no business portfolios available to connect to this app", go to your [Meta Business Suite Settings](https://business.facebook.com/latest/settings/business_users) and make sure you've enabled Two-Factor Authentication (2FA) for your account.
  </Step>

  <Step title="Finish creating the app">
    You should not have any extra requirements to publish and maintain access.
  </Step>

  <Step title="Configure API access">
    On the app dashboard, Select "Access the Threads API" to begin customizing the API access.

    Add the "threads\_content\_publish" and "threads\_basic" (automatically selected) to your app.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b708b39373dec8bb6eb5321ea3b0ec0d" alt="Configure API access" data-og-width="996" width="996" data-og-height="314" height="314" data-path="images/providers/threads/threads_access.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ab5eadddd50df030b9129674d015e351 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f10f07da32dc957e044a34f335fe8234 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8cce9f37e80383ca3096c0d3cd87e39d 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5449e76a9799deaf91701c551bdfc540 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0243fe272f15e7f5d1cf7b224b35a0e1 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_access.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=969c1c532121af51d525421e9c647388 2500w" />
  </Step>

  <Step title="Set threads API settings">
    * Click the "Settings" tab. Copy your "Threads App ID", and set the postiz environment variables `THREADS_APP_ID` to this value.
    * Click the "Show" button next to the Threads App Secret, and set the postiz environment variable `THREADS_APP_SECRET` to this value. Note that the next box is quite small, make sure you scroll across the copy the full value. It should be 32 characters long.

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `threads` in the redirect URI.

    <Note>
      You have to "click" the URL to make it active, otherwise the form will not save.
      You can use the same value for the **Uninstall Callback URL** and **Delete Callback URL**, but note that Postiz does not support either at this time. The form will not save unless you enter something.
    </Note>

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=17fb560ad8fb4a7ddfdf0518dee64b97" alt="Set threads API settings" data-og-width="1022" width="1022" data-og-height="533" height="533" data-path="images/providers/threads/threads_api_settings.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b34fc89b59cf388bf4c46452e2f403a8 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=70239db1fc0126b54fb9e670c838f4a6 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=0129bd533177f4cb49bcf7b0ec17d00f 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a7b386a474c7c8313c40df935d9882e3 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d4dfdeaf211e13eb500a18244e87efdc 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_settings.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=da72ff8e3d185cc642840e6193a54e21 2500w" />

    Go back to the 'Dashboard' view of the Facebook developers portal and click "Finish customization". Make sure you clicked through the setup wizard, and select "Yes I'm finished" to complete the setup. The API may not work until you've done this.
  </Step>

  <Step title="Restart Postiz">
    Stop Postiz if it is running, and then start it again to pick up the new environment variables.

    You should not try to add a Threads account to Postiz at this time.
  </Step>

  <Step title="Add the Threads account as a tester">
    * In the sidebar go to "App roles" -> "roles".
    * Select the "Testers" tab. Click "Add People".
    * Under *Additional Roles for this App*, select *Threads Tester*.
    * Enter usernames of the threads users you want to test the app. Probably your own username. Note that this is probably different from your Meta developers account which is tied to Facebook.
  </Step>

  <Step title="Allow the app on your threads account">
    * On threads.com, open your [account settings](https://www.threads.net/settings/account);
    * Open [Website permissions](https://www.threads.com/settings/website_permissions), and switch to the "Invites" tab;
    * If all has gone well, you should have a pending invite. Accept that invite.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1e77e4f0b5aa7f3d842690f421547741" alt="Threads invite" data-og-width="736" width="736" data-og-height="446" height="446" data-path="images/providers/threads/threads_invite.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2632d82fd7146bce4b013db258ca4b1d 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=287f81ba25f34427f518fa04df3791ef 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=9bc466c01e39b126bb212faaba6f87bc 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=11ae901ec2e0c94e1d1048a5658d1bb1 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=c670288dd377bf078e15d3465c3d5f86 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_invite.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=71c53bf8bba249b8bb18ae673a1a55fc 2500w" />
  </Step>

  <Step title="Start testing">
    * Go back to the Meta developers portal, and in the sidebar, click *Testing*, and then open the *Open Graph API Explorer*.
    * In the header, dropdown the API selector and change it to threads.net v1.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f3df813f870c12aa20b5aa29f81b0c52" alt="API Version" data-og-width="598" width="598" data-og-height="199" height="199" data-path="images/providers/threads/threads_api_version.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d331ffc281262b576937028015c2eee2 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4db90f6ee67c6767d5400c18ef188d1b 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8a46c84cf60db734aacfcf2c15aff43e 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=830a08ad415c056ca95b1ad9aede44b6 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=d49fd2e5ea0be3dc401f427d815ddf18 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/threads/threads_api_version.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=28d1b92c40919fef02f3162f81bbc872 2500w" />

    * In the right sidebar, under *Access Token*, click *Generate Threads Access Token*. This will open a new window where you can select the Threads account you want to test with - it should be an account that accepted the earlier invite. If everything has worked successfully you should be provided with a very long alphanumeric access token - you do not need to do anything with this, but it proves things are working correctly.
  </Step>

  <Step title="Good luck!">
    At this stage things should be working correctly, try a test post from Postiz to confirm.

    This is a complex integration, and it may take some time to get it right. If you have any issues, please reach out to us on the Postiz Discord.
  </Step>
</Steps>


# TikTok
Source: https://docs.postiz.com/providers/tiktok

How to add TikTok to your system

<Snippet file="never-share.mdx" />

<Note>
  This integration requires that you have a TikTok developer account. It also requires that you have a public website, with https, and can upload files to that site to verify ownership.

  TikTok will also not allow http\:// for your app redirect URI, so you will need to be accessing Postiz from HTTPS.
</Note>

<Warning>
  **NOTE:** TikTok fetches media via pull\_from\_url. Your media files must be publicly reachable over HTTPS; localhost or private routes (e.g., /uploads) will fail.
  Expose your uploads via a reverse proxy (e.g., [Caddy](/reverse-proxies/caddy)) or use object storage/CDN such as [Cloudflare R2](/configuration/r2) with public access.

  Ensure the media domain is listed under your TikTok developer account's verified sites.
</Warning>

<Steps>
  <Step title="Create your app">
    Go here: [https://developers.tiktok.com/apps](https://developers.tiktok.com/apps)

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=21d52883579c58401d0487bf3b8d9cf0" alt="Create a new app" data-og-width="400" width="400" data-og-height="527" height="527" data-path="images/providers/tiktok/tiktok_create_app.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=9135f6fcd134a3c0ebab8b8ee856eb24 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f4d10e7a8288b3a1072ce18bd42d9594 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7aeff4540920e7486057e11aad0ff239 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3a6881a62c4a50e85c7ae77efefbda14 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f3de0862221f04861147b1f572b4b854 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/tiktok/tiktok_create_app.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6eca8689d4f13dd08a3c4d11077e40f2 2500w" />

    * **App Name:** `MyPostiz`
    * **Redirect URI:** (see below)

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `tiktok` in the redirect URI.
  </Step>

  <Step title="Set a TOS and Privacy Policy">
    This needs to be on a public domain that you have access to, that is hosted on HTTPS.

    Tick "Web" for your platforms.
  </Step>

  <Step title="Add apps">
    Add the "Login Kit" and "Content Posting API" to your app.

    For "Login Kit", set the redirect URI to [http://localhost:4200/integrations/social/tiktok](http://localhost:4200/integrations/social/tiktok)

    For Content posting API, enable "Direct Post".
  </Step>

  <Step title="Add scopes">
    Add the following scopes:

    * user.info.basic
    * video.create
    * video.publish
    * video.upload
    * user.info.profile
  </Step>

  <Step title="Copy your client secret to environment variables">
    These can be found immediately after registering your application. The client ID is 16 characters long and the secret is 32 characters long.

    ```env  theme={null}
    TIKTOK_CLIENT_ID=1234567890123456
    TIKTOK_CLIENT_SECRET=12345678901234567890123456789012
    ```

    Restart Postiz with the updated environment variables
  </Step>

  <Step title="Add a TikTok channel in the Postiz web interface">
    Go to the Postiz web interface, and click on the "Add Channel" button. Select "TikTok" from the list of available channels. You should be redirected to TikTok to authorize the application.
  </Step>
</Steps>


# X (Twitter)
Source: https://docs.postiz.com/providers/x-twitter

How to add X to your system

<Snippet file="never-share.mdx" />

<Note>
  Watch the YouTube Tutorial: [https://m.youtube.com/watch?si=swqzAXiSTFOXZiFo\&v=3WneMPnOu88](https://m.youtube.com/watch?si=swqzAXiSTFOXZiFo\&v=3WneMPnOu88)
</Note>

X is a bit different.

They created an oAuth2 flow, but it works only with Twitter v2 API.
But in order to upload pictures to X, you need to use the old Twitter v1 API.
So you are going to use the normal oAuth1 flow for that (that supports Twitter v2 also 🤷🏻‍).

<Steps>
  <Step title="Create a new app">
    Head over the [Twitter developers page](https://developer.twitter.com/en/portal/dashboard) and create a new app.
    Click to sign-up for a new free account

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=94589e4e4357a0357a5e4da4a7f4326f" alt="X" data-og-width="1238" width="1238" data-og-height="368" height="368" data-path="images/providers/x/x-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4b9698f22069ed5fcfcf2bbebe184305 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fbd42fa5ab8e18ddd6a083fed88c3c1c 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1e3c676daa78699570480368c30af9b0 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=299304227c2fe8fe948a3b7c259347a1 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=1cb3b3741d10d30bc0418175167bc1fc 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3668d50232cbbb6ca465d756010fb73b 2500w" />
  </Step>

  <Step title="Edit application settings">
    Click to edit the application settings

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=fdf900423074b1e1549aa66c5df49123" alt="X" data-og-width="1660" width="1660" data-og-height="852" height="852" data-path="images/providers/x/x-002.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=cfa74608e3a70ac3427364a5d013acec 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=36ee7cb603ad918479e0977becf102c4 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4d6c627dd348c325573d20671db54eb1 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=aa97ad699a52b29acb8eb8393a776801 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4f70d78aa0147fe4b3208440f1b819a1 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-002.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=54b6440ee100c2219322a4a58f42bf54 2500w" />
  </Step>

  <Step title="Set up authentication flow">
    Click to set up an authentication flow

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=653a11fc92ed3c7e0bab5dd721fbd4ff" alt="X" data-og-width="1668" width="1668" data-og-height="420" height="420" data-path="images/providers/x/x-003.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3409b80bdb0e6ad6096b1fc822b7d845 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=5e69acc987f5c03c494bdaeb3617afc2 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=8ad99a46b78570950872b885438bee71 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=32c8b84d5d245367ceea77da112fb87d 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6f4bbbac5b799ec12b4ca932c9193a7a 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/x/x-003.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ea8347ac39a64dea6956f0f74b8f956f 2500w" />

    * In the App Permission set it to `Read and Write`
    * In the Type of App set it to `Web App, Automated App or Bot`
    * In the App Info set the `Callback URI / Redirect URL`
  </Step>

  <Step title="Configure OAuth2 Redirect URI">
    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `x` in the redirect URI.
  </Step>

  <Step title="Copy your API keys">
    Save it and go to "Keys and Tokens" tab.

    Click on "Regenerate" inside "Consumer Keys" and copy the `API Key` and `API Key Secret`.

    Open .env file and add the following:

    ```env  theme={null}
    X_API_KEY=""
    X_API_SECRET=""
    ```
  </Step>
</Steps>


# YouTube
Source: https://docs.postiz.com/providers/youtube

How to Add YouTube to Your System

<Snippet file="never-share.mdx" />

<Note>
  Watch the YouTube Tutorial: [https://youtu.be/b8fxx6DqIAw](https://youtu.be/b8fxx6DqIAw)
</Note>

Follow the instructions as available in the [Obtaining authorization credentials](https://developers.google.com/youtube/registering_an_application).

## General Setup

<Steps>
  <Step title="Go to Credentials Page">
    Make sure you are logged in to your Google account and visit the [Credentials - APIs & Services](https://console.cloud.google.com/projectselector2/apis/credentials) page. Make sure to read the terms and conditions and "Agree and Continue".
  </Step>

  <Step title="Create Project">
    Create a new project by clicking on the "Create Project" button. Fill in the project name, and details and click "Create".
  </Step>

  <Step title="Create OAuth Credentials">
    Create credentials by clicking on the "Create Credentials" button. Select the "OAuth client ID" option.
  </Step>

  <Step title="Configure Consent Screen">
    Make sure that your consent screen has been configured.
  </Step>

  <Step title="Fill in OAuth Details">
    Create the OAuth client ID. Select "Web application" as the application type and fill in the details.

    <Snippet file="oauth2redirect.mdx" />

    Replace `{provider}` with `youtube` in the redirect URI.

    Under "Authorized redirect URIs", insert your OAuth2 Redirect URI.

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=ed4f80902b3cf6c9005f53eceadaf016" alt="YouTube" data-og-width="784" width="784" data-og-height="733" height="733" data-path="images/providers/youtube/youtube-001.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3ba050f2fa1497df0dd594c088871af8 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=633ba4c05f93a06197686dafd27c137c 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7c9fa8db55dbf8b7a3a012d252e0b62c 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3dc4f3f9a6a870496c05ca233654ab7c 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a7dcc96b200bb711e0f0103bf7de2390 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/youtube-001.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=06716f85cc182faa98709b9431a23dfa 2500w" />

    After following all of the steps above you should be met with a screen that shows your client ID and client secret. Add these to your providers configuration.

    ```env  theme={null}
    YOUTUBE_CLIENT_ID=""
    YOUTUBE_CLIENT_SECRET=""
    ```
  </Step>

  <Step title="Add Test User">
    Add yourself as a test user of the application

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6d7922a5a7f4f78277989e157478e9d8" alt="YouTube" data-og-width="1477" width="1477" data-og-height="885" height="885" data-path="images/providers/youtube/YouTube-Add-Test-Users.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=f211d49fb9fcb19d671a19aff666d5e6 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4fa0ad92c96483a899151551c9a92fce 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=db4dc83e4744e471cf116bcfd67d2dc2 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=4bfc96b21b9a3f4ffced49f9156a0b77 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=44430345a23a9d78fc05df678825d249 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Add-Test-Users.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=562d9247c4ace304be3113c3bee89813 2500w" />
  </Step>

  <Step title="Activate YouTube's API">
    Go to "Enabled APIs and Services". Then click on "Enable APIs and Services". Search "YouTube Data API v3" and activate the API by selecting it and clicking "Enable". Do the same Process with "YouTube Analytics API" and "YouTube Reporting API".

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2faa5c8897cdec07c27389e2475e5b03" alt="Enabled APIs" data-og-width="1918" width="1918" data-og-height="867" height="867" data-path="images/providers/youtube/YouTube-Enabled-APIs.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=99e5b0d1bae6c8bd85d7394588472444 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=e35d1f2c1e0736906ea3cf7e54d374a0 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=059073c4a2110cc127db88a371ab42ce 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=a8d25334238fcca20ceddaedcc363785 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3488e31830baffff16e0ba480a90ff19 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/YouTube-Enabled-APIs.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=2e78ebea3f5f4255075cbe77203e9ebb 2500w" />

        <img src="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=7ffb5b0e1fc5d1ccde0cd2b33e2e9eed" alt="Enabled APIs" data-og-width="1136" width="1136" data-og-height="367" height="367" data-path="images/providers/youtube/Enable-APIs-and-Services.png" data-optimize="true" data-opv="3" srcset="https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?w=280&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=94e752ca66b4bdaf0082c0bc91ee5fe8 280w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?w=560&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b3b65b3b6a4e4b293565f67a8f60b129 560w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?w=840&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=b88caceddf098466010ef421c8d9d14e 840w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?w=1100&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=6b9753b01633ec7e7ada22772df804be 1100w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?w=1650&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=3f8ab1e54e889294284019929b2cce51 1650w, https://mintcdn.com/postiz/NC1G4tduhaxm8LzE/images/providers/youtube/Enable-APIs-and-Services.png?w=2500&fit=max&auto=format&n=NC1G4tduhaxm8LzE&q=85&s=47458619820905166c4d0ca415d055b7 2500w" />
  </Step>
</Steps>

## Additional Steps for Brand Accounts

<Note>
  When using a Brand account you will need to set your APP to External and setup a test user. You do not need to publish the APP, but it does take time for the changes to propagate. You will also need to add the app to the trusted apps within your google workspace Admin.
</Note>

<Steps>
  <Step title="Go to admin.google.com">
    Sign in
  </Step>

  <Step title="Go to Security Settings">
    Go to Security → Access and data Controls → API Controls
    Click Manage Third Party App Access
  </Step>

  <Step title="Configure new App">
    Click "Configure new App"
    Put your Client ID for the app you created in previous steps into the search box and select your app
  </Step>

  <Step title="Set scopes and Google Data Access">
    Set scopes and Google Data Access for the app to Trusted
    Once set, click save
  </Step>

  <Step title="Wait for propagation">
    Wait at least 5 hours for the changes to propagate.
    After this time you should now be able to add your YouTube channel to your Postiz account
  </Step>
</Steps>


# Find Available Slot
Source: https://docs.postiz.com/public-api/integrations/find-slot

GET /find-slot/{id}
Get the next available time slot for posting to a specific channel.



# Check Connection
Source: https://docs.postiz.com/public-api/integrations/is-connected

GET /is-connected
Verify if your API key is valid and connected.



# List Integrations
Source: https://docs.postiz.com/public-api/integrations/list

GET /integrations
Returns all connected social media channels for your organization.



# API Overview
Source: https://docs.postiz.com/public-api/introduction

Getting started with the Postiz Public API

<Warning>
  This API is currently in Beta and in active development. It does not provide all Postiz features yet.
</Warning>

## SDKs & Integrations

<CardGroup cols={2}>
  <Card title="NodeJS SDK" icon="node-js" href="https://www.npmjs.com/package/@postiz/node">
    Official Postiz NodeJS SDK
  </Card>

  <Card title="n8n Node" icon="n" href="https://www.npmjs.com/package/n8n-nodes-postiz">
    Custom n8n node for Postiz
  </Card>
</CardGroup>

## Authentication

All API requests require an API key. Get your API key from Postiz Settings.

Include the API key in the `Authorization` header:

```bash  theme={null}
curl -H "Authorization: your-api-key" https://api.postiz.com/public/v1/integrations
```

## Base URL

| Environment  | Base URL                                      |
| ------------ | --------------------------------------------- |
| Postiz Cloud | `https://api.postiz.com/public/v1`            |
| Self-hosted  | `https://{NEXT_PUBLIC_BACKEND_URL}/public/v1` |

## Rate Limits

<Info>
  **30 requests per hour** limit applies to all endpoints.

  This doesn't mean you can only post 30 times per hour—each API call counts as one request. Schedule multiple posts in a single request to maximize throughput.
</Info>

## Terminology

<Note>
  The Postiz UI uses the term **channel**, while the API uses **integration**. They refer to the same thing—a connected social media account.
</Note>

## Supported Platforms (27 total)

When creating posts, each social media platform has its own settings schema. The `settings` object must include a `__type` field matching the provider.

### Platforms with custom settings (21)

<Tabs>
  <Tab title="Social">
    | Platform              | `__type`               | Key settings                      |
    | --------------------- | ---------------------- | --------------------------------- |
    | X (Twitter)           | `x`                    | `who_can_reply_post`, `community` |
    | LinkedIn              | `linkedin`             | `post_as_images_carousel`         |
    | LinkedIn Page         | `linkedin-page`        | `post_as_images_carousel`         |
    | Facebook              | `facebook`             | `url` (optional)                  |
    | Instagram (FB-linked) | `instagram`            | `post_type`, `collaborators`      |
    | Instagram Standalone  | `instagram-standalone` | `post_type`, `collaborators`      |
    | Warpcast (Farcaster)  | `warpcast`             | `subreddit[]` (channels)          |
  </Tab>

  <Tab title="Video">
    | Platform | `__type`  | Key settings                                                                                                                           |
    | -------- | --------- | -------------------------------------------------------------------------------------------------------------------------------------- |
    | YouTube  | `youtube` | `title`, `type`, `selfDeclaredMadeForKids`, `thumbnail`, `tags`                                                                        |
    | TikTok   | `tiktok`  | `privacy_level`, `duet`, `stitch`, `comment`, `autoAddMusic`, `brand_content_toggle`, `brand_organic_toggle`, `content_posting_method` |
  </Tab>

  <Tab title="Community">
    | Platform | `__type`  | Key settings                                |
    | -------- | --------- | ------------------------------------------- |
    | Reddit   | `reddit`  | `subreddit[]` with `title`, `type`, `flair` |
    | Lemmy    | `lemmy`   | `subreddit[]` with `id`, `title`, `url`     |
    | Discord  | `discord` | `channel`                                   |
    | Slack    | `slack`   | `channel`                                   |
  </Tab>

  <Tab title="Design">
    | Platform  | `__type`    | Key settings                               |
    | --------- | ----------- | ------------------------------------------ |
    | Pinterest | `pinterest` | `board`, `title`, `link`, `dominant_color` |
    | Dribbble  | `dribbble`  | `title`, `team`                            |
  </Tab>

  <Tab title="Blogging">
    | Platform  | `__type`    | Key settings                                               |
    | --------- | ----------- | ---------------------------------------------------------- |
    | Medium    | `medium`    | `title`, `subtitle`, `canonical`, `publication`, `tags`    |
    | Dev.to    | `devto`     | `title`, `main_image`, `canonical`, `organization`, `tags` |
    | Hashnode  | `hashnode`  | `title`, `subtitle`, `main_image`, `publication`, `tags`   |
    | WordPress | `wordpress` | `title`, `main_image`, `type`                              |
  </Tab>

  <Tab title="Business">
    | Platform           | `__type`   | Key settings                                                           |
    | ------------------ | ---------- | ---------------------------------------------------------------------- |
    | Google My Business | `gmb`      | `topicType`, `callToActionType`, `callToActionUrl`, event/offer fields |
    | Listmonk           | `listmonk` | `subject`, `preview`, `list`, `template`                               |
  </Tab>
</Tabs>

### Platforms without custom settings (6)

These platforms only require `{ "__type": "platform-name" }`:

| Platform | `__type`   |
| -------- | ---------- |
| Threads  | `threads`  |
| Mastodon | `mastodon` |
| Bluesky  | `bluesky`  |
| Telegram | `telegram` |
| Nostr    | `nostr`    |
| VK       | `vk`       |

<Card title="View Provider Settings Reference" icon="code" href="/public-api/providers/x">
  See detailed settings schemas with examples for each platform
</Card>

## Quick Examples

### Schedule a post to X (Twitter)

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": { "id": "your-integration-id" },
      "value": [
        {
          "content": "Hello from the Postiz API! 🚀",
          "image": []
        }
      ],
      "settings": {
        "__type": "x",
        "who_can_reply_post": "everyone"
      }
    }
  ]
}
```

### Post immediately to LinkedIn

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": { "id": "your-linkedin-id" },
      "value": [
        {
          "content": "Exciting announcement! 🎉",
          "image": []
        }
      ],
      "settings": {
        "__type": "linkedin"
      }
    }
  ]
}
```

### Upload an image and post to Instagram

```bash  theme={null}
# Step 1: Upload the image
curl -X POST "https://api.postiz.com/public/v1/upload" \
  -H "Authorization: your-api-key" \
  -F "file=@photo.jpg"

# Response: { "id": "img-123", "path": "https://uploads.postiz.com/photo.jpg", ... }

# Step 2: Create the post with the uploaded image
curl -X POST "https://api.postiz.com/public/v1/posts" \
  -H "Authorization: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "schedule",
    "date": "2024-12-14T10:00:00.000Z",
    "shortLink": false,
    "tags": [],
    "posts": [{
      "integration": { "id": "your-instagram-id" },
      "value": [{
        "content": "Beautiful sunset 🌅 #photography",
        "image": [{ "id": "img-123", "path": "https://uploads.postiz.com/photo.jpg" }]
      }],
      "settings": {
        "__type": "instagram",
        "post_type": "post"
      }
    }]
  }'
```

### Publish a Medium article

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": { "id": "your-medium-id" },
      "value": [
        {
          "content": "# Introduction\n\nThis is my article in markdown...",
          "image": []
        }
      ],
      "settings": {
        "__type": "medium",
        "title": "My Amazing Article",
        "subtitle": "A deep dive into something interesting",
        "tags": [
          { "value": "programming", "label": "Programming" }
        ]
      }
    }
  ]
}
```

### Create a Google My Business offer

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": { "id": "your-gmb-id" },
      "value": [
        {
          "content": "🎉 Holiday Sale! 20% off everything!",
          "image": []
        }
      ],
      "settings": {
        "__type": "gmb",
        "topicType": "OFFER",
        "callToActionType": "GET_OFFER",
        "callToActionUrl": "https://example.com/sale",
        "offerCouponCode": "HOLIDAY20"
      }
    }
  ]
}
```


# Create Post
Source: https://docs.postiz.com/public-api/posts/create

POST /posts
Create or schedule a new post. Each social media platform has its own settings schema.

## Provider-Specific Settings

When creating posts, each social media platform requires different settings. The `settings` object must include a `__type` field that identifies the platform.

### All 27 Supported Platforms

<Tabs>
  <Tab title="Social Platforms">
    | Platform              | `__type`               | Required Settings                        |
    | --------------------- | ---------------------- | ---------------------------------------- |
    | X (Twitter)           | `x`                    | `who_can_reply_post`                     |
    | LinkedIn              | `linkedin`             | -                                        |
    | LinkedIn Page         | `linkedin-page`        | -                                        |
    | Facebook              | `facebook`             | - (optional: `url`)                      |
    | Instagram (FB-linked) | `instagram`            | `post_type`                              |
    | Instagram Standalone  | `instagram-standalone` | `post_type`                              |
    | Threads               | `threads`              | -                                        |
    | Bluesky               | `bluesky`              | -                                        |
    | Mastodon              | `mastodon`             | -                                        |
    | Warpcast (Farcaster)  | `warpcast`             | - (optional: `subreddit[]` for channels) |
    | Nostr                 | `nostr`                | -                                        |
    | VK                    | `vk`                   | -                                        |
  </Tab>

  <Tab title="Video Platforms">
    | Platform | `__type`  | Required Settings                                                                                                                      |
    | -------- | --------- | -------------------------------------------------------------------------------------------------------------------------------------- |
    | YouTube  | `youtube` | `title`, `type`                                                                                                                        |
    | TikTok   | `tiktok`  | `privacy_level`, `duet`, `stitch`, `comment`, `autoAddMusic`, `brand_content_toggle`, `brand_organic_toggle`, `content_posting_method` |
  </Tab>

  <Tab title="Community Platforms">
    | Platform | `__type`   | Required Settings     |
    | -------- | ---------- | --------------------- |
    | Reddit   | `reddit`   | `subreddit[]` (array) |
    | Lemmy    | `lemmy`    | `subreddit[]` (array) |
    | Discord  | `discord`  | `channel`             |
    | Slack    | `slack`    | `channel`             |
    | Telegram | `telegram` | -                     |
  </Tab>

  <Tab title="Design Platforms">
    | Platform  | `__type`    | Required Settings |
    | --------- | ----------- | ----------------- |
    | Pinterest | `pinterest` | `board`           |
    | Dribbble  | `dribbble`  | `title`           |
  </Tab>

  <Tab title="Blogging Platforms">
    | Platform  | `__type`    | Required Settings   |
    | --------- | ----------- | ------------------- |
    | Medium    | `medium`    | `title`, `subtitle` |
    | Dev.to    | `devto`     | `title`             |
    | Hashnode  | `hashnode`  | `title`, `tags[]`   |
    | WordPress | `wordpress` | `title`, `type`     |
  </Tab>

  <Tab title="Business">
    | Platform               | `__type`   | Required Settings                             |
    | ---------------------- | ---------- | --------------------------------------------- |
    | Google My Business     | `gmb`      | - (optional: `topicType`, `callToActionType`) |
    | Listmonk (Newsletters) | `listmonk` | `subject`, `preview`, `list`                  |
  </Tab>
</Tabs>

### Platforms Without Custom Settings

These platforms only need the `__type` field:

```json  theme={null}
{
  "settings": {
    "__type": "threads"
  }
}
```

Platforms: `threads`, `mastodon`, `bluesky`, `telegram`, `nostr`, `vk`

## Detailed Provider Settings

See the [Provider Settings](/public-api/providers/x) section for detailed schemas and examples for each platform.


# Delete Post
Source: https://docs.postiz.com/public-api/posts/delete

DELETE /posts/{id}
Delete a post by ID.



# List Posts
Source: https://docs.postiz.com/public-api/posts/list

GET /posts
Get posts within a date range.



# Dev.to Settings
Source: https://docs.postiz.com/public-api/providers/devto

Provider settings for Dev.to articles

## Overview

Dev.to is a community platform for developers. When posting articles, you can set the title, cover image, tags, and organization.

## Settings Schema

```json  theme={null}
{
  "__type": "devto",
  "title": "Article Title",
  "main_image": {
    "id": "image-id",
    "path": "https://uploads.postiz.com/cover.jpg"
  },
  "canonical": "https://original-url.com/article",
  "organization": "org-id",
  "tags": [
    { "value": "javascript", "label": "javascript" }
  ]
}
```

## Properties

| Property          | Type   | Required | Description                 |
| ----------------- | ------ | -------- | --------------------------- |
| `__type`          | string | ✅        | Must be `"devto"`           |
| `title`           | string | ✅        | Article title (min 2 chars) |
| `main_image`      | object | ❌        | Cover image                 |
| `main_image.id`   | string | ✅        | Image ID                    |
| `main_image.path` | string | ✅        | Image URL                   |
| `canonical`       | string | ❌        | Original URL for SEO        |
| `organization`    | string | ❌        | Organization ID             |
| `tags`            | array  | ❌        | Up to 4 tags                |
| `tags[].value`    | string | ✅        | Tag identifier              |
| `tags[].label`    | string | ✅        | Tag display name            |

## Example

````json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-devto-integration-id"
      },
      "value": [
        {
          "content": "# Getting Started\n\nIn this tutorial, we'll explore...\n\n```javascript\nconst hello = 'world';\nconsole.log(hello);\n```\n\n## Conclusion\n\nThanks for reading!",
          "image": []
        }
      ],
      "settings": {
        "__type": "devto",
        "title": "10 JavaScript Tips for Better Code",
        "tags": [
          { "value": "javascript", "label": "javascript" },
          { "value": "webdev", "label": "webdev" },
          { "value": "tutorial", "label": "tutorial" },
          { "value": "beginners", "label": "beginners" }
        ]
      }
    }
  ]
}
````

## Notes

* Article content supports Markdown with code blocks
* Maximum of 4 tags allowed
* Tags should be lowercase and match Dev.to's existing tags
* If posting under an organization, provide the `organization` ID


# Discord Settings
Source: https://docs.postiz.com/public-api/providers/discord

API settings for posting to Discord

## Settings Schema

When posting a message to Discord, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "discord",
    "channel": "channel-id"
  }
}
```

## Fields

| Field     | Type     | Required | Description        |
| --------- | -------- | -------- | ------------------ |
| `__type`  | `string` | Yes      | Must be `discord`  |
| `channel` | `string` | Yes      | Discord channel ID |

### `channel`

The Discord channel ID where the message will be posted. This is required.

<Note>
  **How to get a Discord channel ID:**

  1. Enable Developer Mode in Discord (User Settings → App Settings → Advanced → Developer Mode)
  2. Right-click on the channel
  3. Click "Copy Channel ID"
</Note>

***

## Complete Example

### Text Message

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-discord-integration-id"
      },
      "value": [
        {
          "content": "🎉 **New Update Released!**\n\nWe've just shipped some exciting new features:\n\n• Feature 1\n• Feature 2\n• Feature 3\n\nCheck it out and let us know what you think!",
          "image": []
        }
      ],
      "settings": {
        "__type": "discord",
        "channel": "1234567890123456789"
      }
    }
  ]
}
```

### Message with Image

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-discord-integration-id"
      },
      "value": [
        {
          "content": "Check out this preview of our new feature! 👀",
          "image": [
            {
              "id": "preview-image-id",
              "path": "https://uploads.postiz.com/preview.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "discord",
        "channel": "1234567890123456789"
      }
    }
  ]
}
```

### Announcement with Formatting

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-discord-integration-id"
      },
      "value": [
        {
          "content": "# 📢 Important Announcement\n\n> We'll be performing scheduled maintenance tonight from 10 PM - 12 AM UTC.\n\n**What to expect:**\n- Brief service interruption\n- Improved performance after maintenance\n\n*Thank you for your patience!*",
          "image": []
        }
      ],
      "settings": {
        "__type": "discord",
        "channel": "9876543210987654321"
      }
    }
  ]
}
```

<Info>
  Discord supports Markdown formatting in messages. You can use:

  * `**bold**` for **bold**
  * `*italic*` for *italic*
  * `# Heading` for headings
  * `> quote` for quotes
  * `` `code` `` for inline code
</Info>


# Dribbble Settings
Source: https://docs.postiz.com/public-api/providers/dribbble

API settings for posting to Dribbble

## Settings Schema

When creating a shot on Dribbble, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "dribbble",
    "title": "My Shot Title",
    "team": ""
  }
}
```

## Fields

| Field    | Type     | Required | Description                      |
| -------- | -------- | -------- | -------------------------------- |
| `__type` | `string` | Yes      | Must be `dribbble`               |
| `title`  | `string` | Yes      | Shot title (min 1 character)     |
| `team`   | `string` | No       | Team URL (if posting for a team) |

### `title`

Required title for your Dribbble shot. This is displayed prominently on your shot.

### `team`

Optional team URL. If you're part of a Dribbble team and want to post on behalf of the team, provide the team URL.

***

## Complete Example

### Personal Shot

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-dribbble-integration-id"
      },
      "value": [
        {
          "content": "New dashboard design concept exploring dark mode aesthetics and modern UI patterns. Built with Figma.\n\n#ui #ux #dashboard #darkmode #design",
          "image": [
            {
              "id": "shot-image-id",
              "path": "https://uploads.postiz.com/dashboard-design.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "dribbble",
        "title": "Dashboard UI - Dark Mode Concept",
        "team": ""
      }
    }
  ]
}
```

### Team Shot

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-dribbble-integration-id"
      },
      "value": [
        {
          "content": "Our latest branding project for TechCorp. Full case study coming soon!\n\n#branding #logo #identity",
          "image": [
            {
              "id": "branding-image-id",
              "path": "https://uploads.postiz.com/branding.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "dribbble",
        "title": "TechCorp Brand Identity",
        "team": "https://dribbble.com/teams/my-design-team"
      }
    }
  ]
}
```

### Mobile App Design

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-dribbble-integration-id"
      },
      "value": [
        {
          "content": "Fitness tracking app concept with focus on clean data visualization and motivational design elements.\n\nSwipe to see all screens →\n\n#mobileapp #fitness #appdesign #ios",
          "image": [
            {
              "id": "app-screen-1",
              "path": "https://uploads.postiz.com/fitness-1.png"
            },
            {
              "id": "app-screen-2",
              "path": "https://uploads.postiz.com/fitness-2.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "dribbble",
        "title": "Fitness App - UI/UX Design",
        "team": ""
      }
    }
  ]
}
```

<Note>
  **Dribbble Tips:**

  * Use high-quality images (800×600 or larger recommended)
  * Include relevant tags in your description
  * Add context about your design process in the content
</Note>


# Facebook Settings
Source: https://docs.postiz.com/public-api/providers/facebook

Provider settings for Facebook posts

## Overview

Facebook integration allows you to post to Facebook pages. You can optionally include a link URL in your posts.

## Settings Schema

```json  theme={null}
{
  "__type": "facebook",
  "url": "https://example.com/my-article"
}
```

## Properties

| Property | Type   | Required | Description                              |
| -------- | ------ | -------- | ---------------------------------------- |
| `__type` | string | ✅        | Must be `"facebook"`                     |
| `url`    | string | ❌        | Optional link URL to include in the post |

## Examples

### Simple text post

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-facebook-integration-id"
      },
      "value": [
        {
          "content": "Check out our latest updates! 🎉",
          "image": []
        }
      ],
      "settings": {
        "__type": "facebook"
      }
    }
  ]
}
```

### Post with link

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-facebook-integration-id"
      },
      "value": [
        {
          "content": "We just published a new blog post about social media automation!",
          "image": []
        }
      ],
      "settings": {
        "__type": "facebook",
        "url": "https://example.com/blog/social-media-automation"
      }
    }
  ]
}
```

### Post with image

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-facebook-integration-id"
      },
      "value": [
        {
          "content": "Beautiful sunset from our office! 🌅",
          "image": [
            {
              "id": "img-123",
              "path": "https://uploads.postiz.com/sunset.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "facebook"
      }
    }
  ]
}
```

## Notes

* The `url` field is optional and creates a link preview in the post
* If posting with images, Facebook will display them as attachments
* Video uploads are also supported


# Google My Business Settings
Source: https://docs.postiz.com/public-api/providers/gmb

Provider settings for Google My Business posts

## Overview

Google My Business (GMB) allows you to create posts that appear on your Google Business Profile. You can create standard updates, events, or offers.

## Settings Schema

```json  theme={null}
{
  "__type": "gmb",
  "topicType": "STANDARD",
  "callToActionType": "LEARN_MORE",
  "callToActionUrl": "https://example.com",
  "eventTitle": "Event Name",
  "eventStartDate": "2024-12-14",
  "eventEndDate": "2024-12-15",
  "eventStartTime": "09:00",
  "eventEndTime": "17:00",
  "offerCouponCode": "SAVE20",
  "offerRedeemUrl": "https://example.com/redeem",
  "offerTerms": "Terms and conditions apply"
}
```

## Properties

| Property           | Type   | Required | Description                                       |
| ------------------ | ------ | -------- | ------------------------------------------------- |
| `__type`           | string | ✅        | Must be `"gmb"`                                   |
| `topicType`        | string | ❌        | Post type: `STANDARD`, `EVENT`, or `OFFER`        |
| `callToActionType` | string | ❌        | CTA button type                                   |
| `callToActionUrl`  | string | ❌        | URL for CTA (required if callToActionType is set) |
| `eventTitle`       | string | ❌        | Event name (required for EVENT type)              |
| `eventStartDate`   | string | ❌        | Event start date                                  |
| `eventEndDate`     | string | ❌        | Event end date                                    |
| `eventStartTime`   | string | ❌        | Event start time                                  |
| `eventEndTime`     | string | ❌        | Event end time                                    |
| `offerCouponCode`  | string | ❌        | Coupon code (for OFFER type)                      |
| `offerRedeemUrl`   | string | ❌        | Redemption URL                                    |
| `offerTerms`       | string | ❌        | Offer terms and conditions                        |

### Call-to-Action Types

* `NONE` - No button
* `BOOK` - Book now
* `ORDER` - Order online
* `SHOP` - Shop now
* `LEARN_MORE` - Learn more
* `SIGN_UP` - Sign up
* `GET_OFFER` - Get offer
* `CALL` - Call

## Examples

### Standard Post

```json  theme={null}
{
  "settings": {
    "__type": "gmb",
    "topicType": "STANDARD",
    "callToActionType": "LEARN_MORE",
    "callToActionUrl": "https://example.com/news"
  }
}
```

### Event Post

```json  theme={null}
{
  "settings": {
    "__type": "gmb",
    "topicType": "EVENT",
    "eventTitle": "Grand Opening",
    "eventStartDate": "2024-12-20",
    "eventEndDate": "2024-12-20",
    "eventStartTime": "10:00",
    "eventEndTime": "18:00",
    "callToActionType": "BOOK",
    "callToActionUrl": "https://example.com/rsvp"
  }
}
```

### Offer Post

```json  theme={null}
{
  "settings": {
    "__type": "gmb",
    "topicType": "OFFER",
    "callToActionType": "GET_OFFER",
    "callToActionUrl": "https://example.com/offer",
    "offerCouponCode": "HOLIDAY20",
    "offerRedeemUrl": "https://example.com/redeem",
    "offerTerms": "Valid until December 31, 2024. Cannot be combined with other offers."
  }
}
```

## Full Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-gmb-integration-id"
      },
      "value": [
        {
          "content": "🎉 Big Holiday Sale! Get 20% off all products this weekend only. Visit us in-store or shop online!",
          "image": [
            {
              "id": "img-id",
              "path": "https://uploads.postiz.com/sale-banner.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "gmb",
        "topicType": "OFFER",
        "callToActionType": "GET_OFFER",
        "callToActionUrl": "https://example.com/holiday-sale",
        "offerCouponCode": "HOLIDAY20",
        "offerTerms": "Valid December 14-15, 2024. In-store and online."
      }
    }
  ]
}
```

## Notes

* GMB posts appear on your Google Business Profile in Search and Maps
* Images are highly recommended for better engagement
* Event posts require date/time information
* Offer posts can include coupon codes for tracking


# Hashnode Settings
Source: https://docs.postiz.com/public-api/providers/hashnode

Provider settings for Hashnode articles

## Overview

Hashnode is a blogging platform for developers. When posting articles, you need to specify the title, tags, and publication.

## Settings Schema

```json  theme={null}
{
  "__type": "hashnode",
  "title": "Article Title",
  "subtitle": "Article Subtitle",
  "main_image": {
    "id": "image-id",
    "path": "https://uploads.postiz.com/cover.jpg"
  },
  "canonical": "https://original-url.com/article",
  "publication": "publication-id",
  "tags": [
    { "value": "tag-id", "label": "Tag Name" }
  ]
}
```

## Properties

| Property          | Type   | Required | Description                    |
| ----------------- | ------ | -------- | ------------------------------ |
| `__type`          | string | ✅        | Must be `"hashnode"`           |
| `title`           | string | ✅        | Article title (min 6 chars)    |
| `subtitle`        | string | ❌        | Article subtitle (min 2 chars) |
| `main_image`      | object | ❌        | Cover image                    |
| `main_image.id`   | string | ✅        | Image ID                       |
| `main_image.path` | string | ✅        | Image URL                      |
| `canonical`       | string | ❌        | Original URL for SEO           |
| `publication`     | string | ✅        | Publication ID                 |
| `tags`            | array  | ✅        | At least 1 tag required        |
| `tags[].value`    | string | ✅        | Tag ID                         |
| `tags[].label`    | string | ✅        | Tag display name               |

## Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-hashnode-integration-id"
      },
      "value": [
        {
          "content": "# Introduction\n\nWelcome to this comprehensive guide...\n\n## Prerequisites\n\n- Node.js installed\n- Basic JavaScript knowledge\n\n## Getting Started\n\nLet's begin...",
          "image": []
        }
      ],
      "settings": {
        "__type": "hashnode",
        "title": "Complete Guide to TypeScript",
        "subtitle": "From basics to advanced patterns",
        "publication": "your-publication-id",
        "tags": [
          { "value": "typescript-id", "label": "TypeScript" },
          { "value": "javascript-id", "label": "JavaScript" }
        ]
      }
    }
  ]
}
```

## Notes

* Title must be at least 6 characters
* At least 1 tag is required
* Publication ID is required - get it from your Hashnode dashboard
* Content supports full Markdown formatting


# Instagram Settings
Source: https://docs.postiz.com/public-api/providers/instagram

API settings for posting to Instagram

## Settings Schema

When creating a post for Instagram, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "instagram",
    "post_type": "post",
    "collaborators": []
  }
}
```

<Note>
  Use `__type: "instagram"` for Facebook Business-linked accounts and `__type: "instagram-standalone"` for standalone Instagram accounts. Both use the same settings schema.
</Note>

## Fields

| Field           | Type     | Required | Description                           |
| --------------- | -------- | -------- | ------------------------------------- |
| `__type`        | `string` | Yes      | `instagram` or `instagram-standalone` |
| `post_type`     | `string` | Yes      | Type of Instagram post                |
| `collaborators` | `array`  | No       | List of collaborator usernames        |

### `post_type`

| Value   | Description                          |
| ------- | ------------------------------------ |
| `post`  | Regular feed post                    |
| `story` | Instagram Story (24-hour visibility) |

### `collaborators`

Array of collaborator objects. Each collaborator will receive an invite to be added as a collaborator on the post.

```json  theme={null}
{
  "collaborators": [
    { "label": "username1" },
    { "label": "username2" }
  ]
}
```

***

## Complete Example

### Feed Post

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-instagram-integration-id"
      },
      "value": [
        {
          "content": "Beautiful sunset today! 🌅\n\n#sunset #photography #nature",
          "image": [
            {
              "id": "image-id",
              "path": "https://uploads.postiz.com/sunset.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "instagram",
        "post_type": "post",
        "collaborators": []
      }
    }
  ]
}
```

### Story Post

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-instagram-integration-id"
      },
      "value": [
        {
          "content": "",
          "image": [
            {
              "id": "story-image-id",
              "path": "https://uploads.postiz.com/story.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "instagram",
        "post_type": "story"
      }
    }
  ]
}
```

### Collaborative Post

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-instagram-integration-id"
      },
      "value": [
        {
          "content": "Amazing collab with @partner! 🤝",
          "image": [
            {
              "id": "collab-image-id",
              "path": "https://uploads.postiz.com/collab.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "instagram",
        "post_type": "post",
        "collaborators": [
          { "label": "partner_username" }
        ]
      }
    }
  ]
}
```

### Carousel Post

Create a carousel by adding multiple images:

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-instagram-integration-id"
      },
      "value": [
        {
          "content": "Swipe to see all the photos! 📸",
          "image": [
            { "id": "img1", "path": "https://uploads.postiz.com/1.jpg" },
            { "id": "img2", "path": "https://uploads.postiz.com/2.jpg" },
            { "id": "img3", "path": "https://uploads.postiz.com/3.jpg" }
          ]
        }
      ],
      "settings": {
        "__type": "instagram",
        "post_type": "post"
      }
    }
  ]
}
```


# Lemmy Settings
Source: https://docs.postiz.com/public-api/providers/lemmy

Provider settings for Lemmy posts

## Overview

Lemmy is a decentralized Reddit alternative. When posting to Lemmy, you need to specify the community and post details.

<Note>
  The settings use `subreddit` as the field name for historical reasons, but it refers to Lemmy communities.
</Note>

## Settings Schema

```json  theme={null}
{
  "__type": "lemmy",
  "subreddit": [
    {
      "value": {
        "subreddit": "community_name",
        "id": "community-id",
        "title": "Post Title",
        "url": "https://optional-link.com"
      }
    }
  ]
}
```

## Properties

| Property                      | Type   | Required | Description                  |
| ----------------------------- | ------ | -------- | ---------------------------- |
| `__type`                      | string | ✅        | Must be `"lemmy"`            |
| `subreddit`                   | array  | ✅        | Array of community targets   |
| `subreddit[].value.subreddit` | string | ✅        | Community name (min 2 chars) |
| `subreddit[].value.id`        | string | ✅        | Community ID                 |
| `subreddit[].value.title`     | string | ✅        | Post title (min 2 chars)     |
| `subreddit[].value.url`       | string | ❌        | Optional link URL            |

## Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-lemmy-integration-id"
      },
      "value": [
        {
          "content": "Check out this interesting article about open-source software!",
          "image": []
        }
      ],
      "settings": {
        "__type": "lemmy",
        "subreddit": [
          {
            "value": {
              "subreddit": "opensource",
              "id": "123456",
              "title": "Why Open Source Matters",
              "url": ""
            }
          }
        ]
      }
    }
  ]
}
```

## Notes

* You can post to multiple communities by adding more items to the `subreddit` array
* Each community requires a unique title for the post
* The `url` field is optional and used for link-type posts


# LinkedIn Settings
Source: https://docs.postiz.com/public-api/providers/linkedin

API settings for posting to LinkedIn

## Settings Schema

When creating a post for LinkedIn (profile or page), use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "linkedin",
    "post_as_images_carousel": false
  }
}
```

<Note>
  Use `__type: "linkedin"` for personal profiles and `__type: "linkedin-page"` for company pages. Both use the same settings schema.
</Note>

## Fields

| Field                     | Type      | Required | Description                           |
| ------------------------- | --------- | -------- | ------------------------------------- |
| `__type`                  | `string`  | Yes      | `linkedin` or `linkedin-page`         |
| `post_as_images_carousel` | `boolean` | No       | Display multiple images as a carousel |

### `post_as_images_carousel`

When set to `true` and you have multiple images, they will be displayed as a swipeable carousel instead of a collage.

| Value   | Description                           |
| ------- | ------------------------------------- |
| `true`  | Images displayed as carousel          |
| `false` | Images displayed as collage (default) |

***

## Complete Example

### Text Post

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-linkedin-integration-id"
      },
      "value": [
        {
          "content": "Excited to share our latest update! 🎉\n\n#innovation #tech",
          "image": []
        }
      ],
      "settings": {
        "__type": "linkedin"
      }
    }
  ]
}
```

### Carousel Post

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-linkedin-integration-id"
      },
      "value": [
        {
          "content": "Check out our product showcase! Swipe through to see all features →",
          "image": [
            {
              "id": "image-1-id",
              "path": "https://uploads.postiz.com/image1.png"
            },
            {
              "id": "image-2-id",
              "path": "https://uploads.postiz.com/image2.png"
            },
            {
              "id": "image-3-id",
              "path": "https://uploads.postiz.com/image3.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "linkedin",
        "post_as_images_carousel": true
      }
    }
  ]
}
```

### Company Page Post

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-linkedin-page-integration-id"
      },
      "value": [
        {
          "content": "We're hiring! Join our team and help us build the future.",
          "image": []
        }
      ],
      "settings": {
        "__type": "linkedin-page",
        "post_as_images_carousel": false
      }
    }
  ]
}
```


# Listmonk Settings
Source: https://docs.postiz.com/public-api/providers/listmonk

Provider settings for Listmonk newsletter campaigns

## Overview

Listmonk is a self-hosted newsletter and mailing list manager. When creating campaigns, you need to specify the subject, preview text, list, and optionally a template.

## Settings Schema

```json  theme={null}
{
  "__type": "listmonk",
  "subject": "Newsletter Subject Line",
  "preview": "Preview text shown in email clients",
  "list": "list-id",
  "template": "template-id"
}
```

## Properties

| Property   | Type   | Required | Description                     |
| ---------- | ------ | -------- | ------------------------------- |
| `__type`   | string | ✅        | Must be `"listmonk"`            |
| `subject`  | string | ✅        | Email subject line (min 1 char) |
| `preview`  | string | ✅        | Preview text for email clients  |
| `list`     | string | ✅        | List ID to send to              |
| `template` | string | ❌        | Template ID to use              |

## Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-listmonk-integration-id"
      },
      "value": [
        {
          "content": "<h1>Weekly Newsletter</h1>\n<p>Here's what happened this week...</p>\n\n<h2>Top Stories</h2>\n<ul>\n<li>Story 1</li>\n<li>Story 2</li>\n</ul>\n\n<p>Thanks for reading!</p>",
          "image": []
        }
      ],
      "settings": {
        "__type": "listmonk",
        "subject": "🚀 Weekly Update - December 2024",
        "preview": "Check out what's new this week!",
        "list": "1",
        "template": "2"
      }
    }
  ]
}
```

## Notes

* Get the list and template IDs from your Listmonk admin dashboard
* Content should be HTML formatted
* The `preview` text appears in email clients before opening the email
* If no template is specified, the default template will be used


# Medium Settings
Source: https://docs.postiz.com/public-api/providers/medium

Provider settings for Medium articles

## Overview

Medium is a publishing platform for articles and stories. When posting to Medium, you can set article metadata, tags, and publication options.

## Settings Schema

```json  theme={null}
{
  "__type": "medium",
  "title": "Article Title",
  "subtitle": "Article Subtitle",
  "canonical": "https://original-url.com/article",
  "publication": "publication-id",
  "tags": [
    { "value": "programming", "label": "Programming" }
  ]
}
```

## Properties

| Property       | Type   | Required | Description                           |
| -------------- | ------ | -------- | ------------------------------------- |
| `__type`       | string | ✅        | Must be `"medium"`                    |
| `title`        | string | ✅        | Article title (min 2 chars)           |
| `subtitle`     | string | ✅        | Article subtitle (min 2 chars)        |
| `canonical`    | string | ❌        | Original URL for SEO (canonical link) |
| `publication`  | string | ❌        | Publication ID to post to             |
| `tags`         | array  | ❌        | Up to 4 tags                          |
| `tags[].value` | string | ✅        | Tag identifier                        |
| `tags[].label` | string | ✅        | Tag display name                      |

## Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-medium-integration-id"
      },
      "value": [
        {
          "content": "# Introduction\n\nThis is my article content written in markdown.\n\n## Section 1\n\nMore content here...",
          "image": []
        }
      ],
      "settings": {
        "__type": "medium",
        "title": "Building Modern Web Applications",
        "subtitle": "A comprehensive guide to full-stack development",
        "tags": [
          { "value": "programming", "label": "Programming" },
          { "value": "webdev", "label": "Web Development" },
          { "value": "javascript", "label": "JavaScript" }
        ]
      }
    }
  ]
}
```

## Notes

* Article content should be written in Markdown format
* Maximum of 4 tags allowed
* If you have a publication, use the `publication` field to post directly to it
* Use `canonical` if the article was originally published elsewhere


# Pinterest Settings
Source: https://docs.postiz.com/public-api/providers/pinterest

API settings for posting to Pinterest

## Settings Schema

When creating a Pin on Pinterest, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "pinterest",
    "board": "board-id",
    "title": "My Pin Title",
    "link": "https://example.com",
    "dominant_color": "#FF5733"
  }
}
```

## Fields

| Field            | Type     | Required | Description                    |
| ---------------- | -------- | -------- | ------------------------------ |
| `__type`         | `string` | Yes      | Must be `pinterest`            |
| `board`          | `string` | Yes      | Board ID to pin to             |
| `title`          | `string` | No       | Pin title (max 100 characters) |
| `link`           | `string` | No       | Destination URL                |
| `dominant_color` | `string` | No       | Dominant color for the pin     |

### `board`

The board ID where the pin will be saved. This is required and must be a valid board ID from your Pinterest account.

<Note>
  Get board IDs by using Postiz's UI to view your boards, or by using Pinterest's API directly.
</Note>

### `title`

Optional pin title with a maximum of 100 characters.

### `link`

Optional destination URL. When users click "Visit" on your pin, they'll be taken to this URL.

### `dominant_color`

Optional hex color code that represents the dominant color of your pin. Pinterest may use this for visual presentation.

***

## Complete Example

### Basic Pin

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-pinterest-integration-id"
      },
      "value": [
        {
          "content": "Beautiful home decor inspiration for your living room! 🏠✨",
          "image": [
            {
              "id": "pin-image-id",
              "path": "https://uploads.postiz.com/decor.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "pinterest",
        "board": "1234567890123456789",
        "title": "Living Room Decor Ideas",
        "link": "https://myblog.com/living-room-decor",
        "dominant_color": ""
      }
    }
  ]
}
```

### Pin with All Options

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-pinterest-integration-id"
      },
      "value": [
        {
          "content": "Try this delicious recipe! Perfect for summer dinners. Full recipe on my blog 👇",
          "image": [
            {
              "id": "recipe-image-id",
              "path": "https://uploads.postiz.com/recipe.jpg"
            }
          ]
        }
      ],
      "settings": {
        "__type": "pinterest",
        "board": "9876543210987654321",
        "title": "Easy Summer Pasta Recipe",
        "link": "https://myrecipes.com/summer-pasta",
        "dominant_color": "#E8B4B8"
      }
    }
  ]
}
```

### Simple Pin (Minimal Settings)

```json  theme={null}
{
  "settings": {
    "__type": "pinterest",
    "board": "1234567890123456789",
    "title": "",
    "link": "",
    "dominant_color": ""
  }
}
```


# Reddit Settings
Source: https://docs.postiz.com/public-api/providers/reddit

API settings for posting to Reddit

## Settings Schema

When creating a post for Reddit, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "reddit",
    "subreddit": [
      {
        "value": {
          "subreddit": "programming",
          "title": "My Post Title",
          "type": "self",
          "url": "",
          "is_flair_required": false,
          "flair": null
        }
      }
    ]
  }
}
```

## Fields

| Field       | Type     | Required | Description                       |
| ----------- | -------- | -------- | --------------------------------- |
| `__type`    | `string` | Yes      | Must be `reddit`                  |
| `subreddit` | `array`  | Yes      | Array of subreddit configurations |

### Subreddit Object

Each item in the `subreddit` array contains a `value` object with:

| Field               | Type      | Required    | Description                                            |
| ------------------- | --------- | ----------- | ------------------------------------------------------ |
| `subreddit`         | `string`  | Yes         | Subreddit name (without r/)                            |
| `title`             | `string`  | Yes         | Post title (min 2 characters)                          |
| `type`              | `string`  | Yes         | Post type                                              |
| `url`               | `string`  | Conditional | URL for link posts                                     |
| `is_flair_required` | `boolean` | Yes         | Whether flair is required                              |
| `flair`             | `object`  | Conditional | Flair object (required if `is_flair_required` is true) |

### `type` (Post Type)

| Value   | Description                              |
| ------- | ---------------------------------------- |
| `self`  | Text post (uses content from post value) |
| `link`  | Link post (requires `url` field)         |
| `image` | Image post                               |
| `video` | Video post                               |

### Flair Object

Required when `is_flair_required` is `true`:

```json  theme={null}
{
  "flair": {
    "id": "flair-template-id",
    "name": "Discussion"
  }
}
```

***

## Complete Example

### Text Post (Self Post)

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-reddit-integration-id"
      },
      "value": [
        {
          "content": "I've been working on this project for the past few months and wanted to share my experience.\n\n## What I learned\n\n1. Planning is crucial\n2. Start small\n3. Iterate quickly\n\nWhat are your thoughts?",
          "image": []
        }
      ],
      "settings": {
        "__type": "reddit",
        "subreddit": [
          {
            "value": {
              "subreddit": "programming",
              "title": "My journey building a side project - lessons learned",
              "type": "self",
              "url": "",
              "is_flair_required": false,
              "flair": null
            }
          }
        ]
      }
    }
  ]
}
```

### Link Post

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-reddit-integration-id"
      },
      "value": [
        {
          "content": "",
          "image": []
        }
      ],
      "settings": {
        "__type": "reddit",
        "subreddit": [
          {
            "value": {
              "subreddit": "technology",
              "title": "Interesting article about AI developments",
              "type": "link",
              "url": "https://example.com/ai-article",
              "is_flair_required": false,
              "flair": null
            }
          }
        ]
      }
    }
  ]
}
```

### Post with Required Flair

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-reddit-integration-id"
      },
      "value": [
        {
          "content": "Looking for advice on my situation...",
          "image": []
        }
      ],
      "settings": {
        "__type": "reddit",
        "subreddit": [
          {
            "value": {
              "subreddit": "personalfinance",
              "title": "Need advice on budgeting",
              "type": "self",
              "url": "",
              "is_flair_required": true,
              "flair": {
                "id": "abc123-flair-id",
                "name": "Budgeting"
              }
            }
          }
        ]
      }
    }
  ]
}
```

### Cross-post to Multiple Subreddits

```json  theme={null}
{
  "settings": {
    "__type": "reddit",
    "subreddit": [
      {
        "value": {
          "subreddit": "webdev",
          "title": "New CSS feature just dropped!",
          "type": "link",
          "url": "https://example.com/css-news",
          "is_flair_required": false,
          "flair": null
        }
      },
      {
        "value": {
          "subreddit": "frontend",
          "title": "New CSS feature just dropped!",
          "type": "link",
          "url": "https://example.com/css-news",
          "is_flair_required": false,
          "flair": null
        }
      }
    ]
  }
}
```

<Note>
  **Tip:** You can post to multiple subreddits simultaneously by adding multiple objects to the `subreddit` array.
</Note>


# Slack Settings
Source: https://docs.postiz.com/public-api/providers/slack

API settings for posting to Slack

## Settings Schema

When posting a message to Slack, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "slack",
    "channel": "channel-id"
  }
}
```

## Fields

| Field     | Type     | Required | Description      |
| --------- | -------- | -------- | ---------------- |
| `__type`  | `string` | Yes      | Must be `slack`  |
| `channel` | `string` | Yes      | Slack channel ID |

### `channel`

The Slack channel ID where the message will be posted. This is required.

<Note>
  **How to get a Slack channel ID:**

  1. Right-click on the channel name in Slack
  2. Click "View channel details"
  3. Scroll to the bottom - the Channel ID is displayed there
  4. Or, the channel ID is in the URL when viewing a channel: `https://app.slack.com/client/WORKSPACE/CHANNEL_ID`
</Note>

***

## Complete Example

### Simple Message

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-slack-integration-id"
      },
      "value": [
        {
          "content": "🎉 *New Release v2.0*\n\nWe're excited to announce our latest update!\n\n• New dashboard design\n• Improved performance\n• Bug fixes\n\nCheck it out: https://example.com/release",
          "image": []
        }
      ],
      "settings": {
        "__type": "slack",
        "channel": "C0123456789"
      }
    }
  ]
}
```

### Message with Image

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-slack-integration-id"
      },
      "value": [
        {
          "content": "📊 *Weekly Metrics Report*\n\nHere's our performance this week:",
          "image": [
            {
              "id": "metrics-image-id",
              "path": "https://uploads.postiz.com/metrics.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "slack",
        "channel": "C0123456789"
      }
    }
  ]
}
```

### Team Announcement

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-slack-integration-id"
      },
      "value": [
        {
          "content": ":mega: *Team Update*\n\n> Reminder: All-hands meeting tomorrow at 2 PM\n\n*Agenda:*\n1. Q4 Review\n2. 2025 Planning\n3. Open Q&A\n\nSee you there! :wave:",
          "image": []
        }
      ],
      "settings": {
        "__type": "slack",
        "channel": "C9876543210"
      }
    }
  ]
}
```

<Info>
  Slack supports its own formatting syntax:

  * `*bold*` for **bold**
  * `_italic_` for *italic*
  * `~strikethrough~` for ~~strikethrough~~
  * `:emoji_name:` for emojis
  * `> quote` for quotes
  * `` `code` `` for inline code
</Info>


# TikTok Settings
Source: https://docs.postiz.com/public-api/providers/tiktok

API settings for posting to TikTok

## Settings Schema

When uploading a video to TikTok, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "tiktok",
    "title": "",
    "privacy_level": "PUBLIC_TO_EVERYONE",
    "duet": false,
    "stitch": false,
    "comment": true,
    "autoAddMusic": "no",
    "brand_content_toggle": false,
    "brand_organic_toggle": false,
    "video_made_with_ai": false,
    "content_posting_method": "DIRECT_POST"
  }
}
```

## Fields

| Field                    | Type      | Required | Description                     |
| ------------------------ | --------- | -------- | ------------------------------- |
| `__type`                 | `string`  | Yes      | Must be `tiktok`                |
| `title`                  | `string`  | No       | Video title (max 90 characters) |
| `privacy_level`          | `string`  | Yes      | Who can view the video          |
| `duet`                   | `boolean` | Yes      | Allow duets                     |
| `stitch`                 | `boolean` | Yes      | Allow stitches                  |
| `comment`                | `boolean` | Yes      | Allow comments                  |
| `autoAddMusic`           | `string`  | Yes      | Auto-add music to video         |
| `brand_content_toggle`   | `boolean` | Yes      | Branded content disclosure      |
| `brand_organic_toggle`   | `boolean` | Yes      | Organic branded content         |
| `video_made_with_ai`     | `boolean` | No       | AI-generated content disclosure |
| `content_posting_method` | `string`  | Yes      | How to post the content         |

### `privacy_level`

| Value                   | Description                    |
| ----------------------- | ------------------------------ |
| `PUBLIC_TO_EVERYONE`    | Anyone can view                |
| `MUTUAL_FOLLOW_FRIENDS` | Only mutual followers can view |
| `FOLLOWER_OF_CREATOR`   | Only your followers can view   |
| `SELF_ONLY`             | Only you can view              |

### `autoAddMusic`

| Value | Description                |
| ----- | -------------------------- |
| `yes` | TikTok will auto-add music |
| `no`  | No automatic music         |

### `content_posting_method`

| Value         | Description               |
| ------------- | ------------------------- |
| `DIRECT_POST` | Post directly to TikTok   |
| `UPLOAD`      | Upload for manual posting |

***

## Complete Example

### Public Video with Interactions Enabled

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-tiktok-integration-id"
      },
      "value": [
        {
          "content": "Check out this awesome tip! 🔥 #tips #viral #fyp",
          "image": [
            {
              "id": "video-id",
              "path": "https://uploads.postiz.com/tiktok-video.mp4"
            }
          ]
        }
      ],
      "settings": {
        "__type": "tiktok",
        "title": "Quick Productivity Tip",
        "privacy_level": "PUBLIC_TO_EVERYONE",
        "duet": true,
        "stitch": true,
        "comment": true,
        "autoAddMusic": "no",
        "brand_content_toggle": false,
        "brand_organic_toggle": false,
        "video_made_with_ai": false,
        "content_posting_method": "DIRECT_POST"
      }
    }
  ]
}
```

### Branded Content Video

```json  theme={null}
{
  "settings": {
    "__type": "tiktok",
    "title": "Partnership with Brand",
    "privacy_level": "PUBLIC_TO_EVERYONE",
    "duet": false,
    "stitch": false,
    "comment": true,
    "autoAddMusic": "no",
    "brand_content_toggle": true,
    "brand_organic_toggle": true,
    "video_made_with_ai": false,
    "content_posting_method": "DIRECT_POST"
  }
}
```

### AI-Generated Content

```json  theme={null}
{
  "settings": {
    "__type": "tiktok",
    "title": "AI Generated Art",
    "privacy_level": "PUBLIC_TO_EVERYONE",
    "duet": true,
    "stitch": true,
    "comment": true,
    "autoAddMusic": "yes",
    "brand_content_toggle": false,
    "brand_organic_toggle": false,
    "video_made_with_ai": true,
    "content_posting_method": "DIRECT_POST"
  }
}
```

### Private Video (Draft)

```json  theme={null}
{
  "settings": {
    "__type": "tiktok",
    "title": "",
    "privacy_level": "SELF_ONLY",
    "duet": false,
    "stitch": false,
    "comment": false,
    "autoAddMusic": "no",
    "brand_content_toggle": false,
    "brand_organic_toggle": false,
    "content_posting_method": "UPLOAD"
  }
}
```

<Warning>
  **Important:** TikTok requires media files to be publicly accessible via HTTPS. Local files or private URLs will fail. Use [Cloudflare R2](/configuration/r2) or similar storage with public access.
</Warning>


# Warpcast (Farcaster) Settings
Source: https://docs.postiz.com/public-api/providers/warpcast

Provider settings for Warpcast/Farcaster posts

## Overview

Warpcast is a client for the Farcaster protocol, a decentralized social network. When posting to Warpcast, you can specify which channels to post to.

<Note>
  The settings use `subreddit` as the field name for historical reasons, but it refers to Farcaster channels.
</Note>

## Settings Schema

```json  theme={null}
{
  "__type": "warpcast",
  "subreddit": [
    {
      "value": {
        "id": "channel-id"
      }
    }
  ]
}
```

## Properties

| Property               | Type   | Required | Description              |
| ---------------------- | ------ | -------- | ------------------------ |
| `__type`               | string | ✅        | Must be `"warpcast"`     |
| `subreddit`            | array  | ❌        | Array of channel targets |
| `subreddit[].value.id` | string | ✅        | Channel ID               |

## Examples

### Simple cast (no channel)

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-warpcast-integration-id"
      },
      "value": [
        {
          "content": "Hello Farcaster! 👋",
          "image": []
        }
      ],
      "settings": {
        "__type": "warpcast"
      }
    }
  ]
}
```

### Cast to a specific channel

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-warpcast-integration-id"
      },
      "value": [
        {
          "content": "Check out this new open-source project! 🚀",
          "image": []
        }
      ],
      "settings": {
        "__type": "warpcast",
        "subreddit": [
          {
            "value": {
              "id": "developers"
            }
          }
        ]
      }
    }
  ]
}
```

### Cast to multiple channels

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-warpcast-integration-id"
      },
      "value": [
        {
          "content": "Excited to announce our new feature! Built with the community in mind.",
          "image": []
        }
      ],
      "settings": {
        "__type": "warpcast",
        "subreddit": [
          {
            "value": {
              "id": "announcements"
            }
          },
          {
            "value": {
              "id": "builders"
            }
          }
        ]
      }
    }
  ]
}
```

### Cast with image

```json  theme={null}
{
  "type": "now",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-warpcast-integration-id"
      },
      "value": [
        {
          "content": "New design preview! What do you think? 🎨",
          "image": [
            {
              "id": "img-123",
              "path": "https://uploads.postiz.com/design.png"
            }
          ]
        }
      ],
      "settings": {
        "__type": "warpcast",
        "subreddit": [
          {
            "value": {
              "id": "design"
            }
          }
        ]
      }
    }
  ]
}
```

## Notes

* Channel IDs are the names/slugs of Farcaster channels
* You can post without specifying channels (posts to your main feed)
* You can post to multiple channels simultaneously
* Images are supported and will be displayed as embeds


# WordPress Settings
Source: https://docs.postiz.com/public-api/providers/wordpress

Provider settings for WordPress posts

## Overview

WordPress integration allows you to publish posts to your WordPress site. You can set the title, featured image, and post type.

## Settings Schema

```json  theme={null}
{
  "__type": "wordpress",
  "title": "Post Title",
  "main_image": {
    "id": "image-id",
    "path": "https://uploads.postiz.com/featured.jpg"
  },
  "type": "post"
}
```

## Properties

| Property          | Type   | Required | Description                                 |
| ----------------- | ------ | -------- | ------------------------------------------- |
| `__type`          | string | ✅        | Must be `"wordpress"`                       |
| `title`           | string | ✅        | Post title (min 2 chars)                    |
| `main_image`      | object | ❌        | Featured image                              |
| `main_image.id`   | string | ✅        | Image ID                                    |
| `main_image.path` | string | ✅        | Image URL                                   |
| `type`            | string | ✅        | Post type (depends on your WordPress setup) |

## Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-wordpress-integration-id"
      },
      "value": [
        {
          "content": "<h2>Introduction</h2>\n<p>Welcome to our latest blog post...</p>\n\n<h2>Main Content</h2>\n<p>Here's what you need to know...</p>",
          "image": []
        }
      ],
      "settings": {
        "__type": "wordpress",
        "title": "Latest Company Updates",
        "type": "post"
      }
    }
  ]
}
```

## Notes

* The `type` field depends on your WordPress configuration (e.g., "post", "page", or custom post types)
* Content can be HTML or Markdown depending on your WordPress setup
* Featured images will be uploaded to your WordPress media library


# X (Twitter) Settings
Source: https://docs.postiz.com/public-api/providers/x

API settings for posting to X/Twitter

## Settings Schema

When creating a post for X (Twitter), use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "x",
    "who_can_reply_post": "everyone",
    "community": ""
  }
}
```

## Fields

| Field                | Type     | Required | Description               |
| -------------------- | -------- | -------- | ------------------------- |
| `__type`             | `string` | Yes      | Must be `x`               |
| `who_can_reply_post` | `string` | Yes      | Who can reply to the post |
| `community`          | `string` | No       | X Community URL           |

### `who_can_reply_post`

Controls who can reply to your post.

| Value            | Description                      |
| ---------------- | -------------------------------- |
| `everyone`       | Anyone can reply                 |
| `following`      | Only people you follow can reply |
| `mentionedUsers` | Only mentioned users can reply   |
| `subscribers`    | Only your subscribers can reply  |
| `verified`       | Only verified accounts can reply |

### `community`

Optional X Community URL. Must match the format:

```
https://x.com/i/communities/1493446837214187523
```

Leave empty or omit to post to your main timeline.

***

## Complete Example

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-x-integration-id"
      },
      "value": [
        {
          "content": "Hello from the Postiz API! 🚀",
          "image": []
        }
      ],
      "settings": {
        "__type": "x",
        "who_can_reply_post": "everyone",
        "community": ""
      }
    }
  ]
}
```

## Thread Example

Create a thread by adding multiple items to the `value` array:

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-x-integration-id"
      },
      "value": [
        {
          "content": "1/ This is the first tweet in my thread",
          "image": []
        },
        {
          "content": "2/ This is the second tweet",
          "image": []
        },
        {
          "content": "3/ And this is the final tweet!",
          "image": []
        }
      ],
      "settings": {
        "__type": "x",
        "who_can_reply_post": "everyone"
      }
    }
  ]
}
```


# YouTube Settings
Source: https://docs.postiz.com/public-api/providers/youtube

API settings for posting to YouTube

## Settings Schema

When uploading a video to YouTube, use the following settings schema:

```json  theme={null}
{
  "settings": {
    "__type": "youtube",
    "title": "My Video Title",
    "type": "public",
    "selfDeclaredMadeForKids": "no",
    "thumbnail": null,
    "tags": []
  }
}
```

## Fields

| Field                     | Type     | Required | Description                    |
| ------------------------- | -------- | -------- | ------------------------------ |
| `__type`                  | `string` | Yes      | Must be `youtube`              |
| `title`                   | `string` | Yes      | Video title (2-100 characters) |
| `type`                    | `string` | Yes      | Video visibility               |
| `selfDeclaredMadeForKids` | `string` | No       | Made for kids declaration      |
| `thumbnail`               | `object` | No       | Custom thumbnail               |
| `tags`                    | `array`  | No       | Video tags                     |

### `title`

Video title with the following constraints:

* Minimum: 2 characters
* Maximum: 100 characters

### `type` (visibility)

| Value      | Description                    |
| ---------- | ------------------------------ |
| `public`   | Anyone can search for and view |
| `unlisted` | Anyone with the link can view  |
| `private`  | Only you can view              |

### `selfDeclaredMadeForKids`

| Value | Description                  |
| ----- | ---------------------------- |
| `yes` | Content is made for kids     |
| `no`  | Content is not made for kids |

### `thumbnail`

Custom thumbnail object (uploaded via the uploads endpoint):

```json  theme={null}
{
  "thumbnail": {
    "id": "thumbnail-id",
    "path": "https://uploads.postiz.com/thumbnail.jpg"
  }
}
```

### `tags`

Array of tag objects for video SEO:

```json  theme={null}
{
  "tags": [
    { "value": "tech", "label": "tech" },
    { "value": "tutorial", "label": "tutorial" }
  ]
}
```

***

## Complete Example

### Public Video with Tags

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-youtube-integration-id"
      },
      "value": [
        {
          "content": "In this video, I'll show you how to use the Postiz API to automate your social media workflow.\n\nTimestamps:\n0:00 Introduction\n1:30 Setup\n5:00 First API call\n10:00 Conclusion",
          "image": [
            {
              "id": "video-id",
              "path": "https://uploads.postiz.com/tutorial.mp4"
            }
          ]
        }
      ],
      "settings": {
        "__type": "youtube",
        "title": "How to Use Postiz API - Complete Tutorial",
        "type": "public",
        "selfDeclaredMadeForKids": "no",
        "tags": [
          { "value": "postiz", "label": "postiz" },
          { "value": "api", "label": "api" },
          { "value": "tutorial", "label": "tutorial" },
          { "value": "social media automation", "label": "social media automation" }
        ]
      }
    }
  ]
}
```

### Video with Custom Thumbnail

```json  theme={null}
{
  "type": "schedule",
  "date": "2024-12-14T10:00:00.000Z",
  "shortLink": false,
  "tags": [],
  "posts": [
    {
      "integration": {
        "id": "your-youtube-integration-id"
      },
      "value": [
        {
          "content": "Full video description here...",
          "image": [
            {
              "id": "video-id",
              "path": "https://uploads.postiz.com/video.mp4"
            }
          ]
        }
      ],
      "settings": {
        "__type": "youtube",
        "title": "My Awesome Video",
        "type": "public",
        "selfDeclaredMadeForKids": "no",
        "thumbnail": {
          "id": "thumb-id",
          "path": "https://uploads.postiz.com/thumbnail.jpg"
        },
        "tags": []
      }
    }
  ]
}
```

### Private/Unlisted Video

```json  theme={null}
{
  "settings": {
    "__type": "youtube",
    "title": "Private Team Update",
    "type": "unlisted",
    "selfDeclaredMadeForKids": "no",
    "tags": []
  }
}
```


# Upload File
Source: https://docs.postiz.com/public-api/uploads/upload-file

POST /upload
Upload a media file using multipart form data.

## Usage

After uploading, use the returned `id` and `path` in your post's `image` array:

```json  theme={null}
{
  "image": [
    {
      "id": "returned-id",
      "path": "returned-path-url"
    }
  ]
}
```


# Upload from URL
Source: https://docs.postiz.com/public-api/uploads/upload-from-url

POST /upload-from-url
Upload a file from an existing URL.



# Video Function
Source: https://docs.postiz.com/public-api/video/function

POST /video/function
Execute video-related functions like loading available voices.

## Get Available Voices

To get the list of available voices for video generation:

```json  theme={null}
{
  "functionName": "loadVoices",
  "identifier": "image-text-slides"
}
```


# Generate Video
Source: https://docs.postiz.com/public-api/video/generate

POST /generate-video
Create AI-generated videos for your posts.



# Quickstart
Source: https://docs.postiz.com/quickstart

Get started with Postiz installation

## Self Hosted installation options

You can choose between the following installation options;

<CardGroup cols={2}>
  <Card title="Docker Compose" icon="docker" href="/installation/docker-compose">
    Recommended option for **users**
  </Card>

  <Card title="Development" icon="code" href="/installation/development">
    Recommended option for **developers**
  </Card>
</CardGroup>

## Advanced installation options

<CardGroup cols={2}>
  <Card title="Docker (standalone)" icon="docker" href="/installation/docker">
    Run from the command line with Docker
  </Card>

  <Card title="Helm" icon="dharmachakra" href="/installation/kubernetes-helm">
    Run with Kubernetes + Helm
  </Card>
</CardGroup>


# Caddy
Source: https://docs.postiz.com/reverse-proxies/caddy

Configure Caddy as a reverse proxy for Postiz

This page briefly explains how to use Caddy to reverse proxy Postiz. A configuration file example is below;

## Example Caddyfile

```caddy  theme={null}
postiz.example.lan {
        reverse_proxy * localhost:5000
        tls internal
}
```

In this example, we have a single port for Postiz, port 5000, which is the internal proxy built into the Docker container images. The `tls internal` directive is used to enable internal TLS for secure communication.

You will need to install the certificate into your browser with this configuration. If you are hosting on a public domain, Caddy allows you to use LetsEncrypt for automatic certificate management. For more information, refer to the [Caddy documentation](https://caddyserver.com/docs/).

## Postiz configuration

To ensure that Postiz works correctly behind a reverse proxy, you may need to adjust its configuration in your .env file / environment variables. The following variables should be set to the correct values:

```bash  theme={null}
MAIN_URL="https://postiz.example.lan"
FRONTEND_URL="https://postiz.example.lan"
NEXT_PUBLIC_BACKEND_URL="https://postiz.example.lan/api"
```


# Nginx
Source: https://docs.postiz.com/reverse-proxies/nginx

Configure Nginx as a reverse proxy for Postiz

<Snippet file="earlydoc.mdx" />

## How to add Nginx as a reverse proxy

<Info>
  The following commands are tailored for Ubuntu/Debian systems. If you're using a different Linux distribution, you may need to adjust package management commands accordingly (e.g., `yum` for CentOS, `brew` for macOS).
</Info>

<Steps>
  <Step title="Install Nginx">
    ```bash  theme={null}
    sudo apt update && sudo apt install nginx -y
    sudo systemctl start nginx && sudo systemctl enable nginx
    ```
  </Step>

  <Step title="Install Certbot for SSL">
    ```bash  theme={null}
    sudo apt update && sudo apt install python3-certbot-nginx -y
    ```
  </Step>

  <Step title="Create the Webroot Directory">
    ```bash  theme={null}
    sudo mkdir -p /var/www/certbot/.well-known/acme-challenge && sudo chown -R www-data:www-data /var/www/certbot
    ```
  </Step>

  <Step title="Create a temporary Nginx config for creating SSL certificates">
    <Note>
      Make sure to change subdomain.domain.tld to your actual subdomain and ensure that it is pointed to your server's IP address.
    </Note>

    Edit the file `/etc/nginx/sites-available/subdomain.domain.tld`:

    ```nginx  theme={null}
    server {
        listen 80;
        listen [::]:80;  # IPv6 support
        server_name <subdomain.domain.tld>;  # CHANGE HERE

        location /.well-known/acme-challenge/ {
            root /var/www/certbot;
        }

        location / {
            return 301 https://$host$request_uri;
        }

        # Hide NGINX version for security reasons
        server_tokens off;
    }
    ```

    Then create the symlink:

    ```bash  theme={null}
    sudo ln -s /etc/nginx/sites-available/<subdomain.domain.tld> /etc/nginx/sites-enabled/
    ```
  </Step>

  <Step title="Obtain SSL Certificates Using Webroot method">
    ```bash  theme={null}
    sudo certbot certonly --webroot -w /var/www/certbot -d <subdomain.domain.tld> --email your-email@example.com --agree-tos --no-eff-email
    ```
  </Step>

  <Step title="Edit the Nginx config">
    Edit the file `/etc/nginx/sites-available/subdomain.domain.tld`.

    <Tabs>
      <Tab title="Secure Config (Recommended)">
        ```nginx  theme={null}
        # HTTP -> HTTPS Redirection
        server {
            listen 80;
            listen [::]:80;  # IPv6 support
            server_name <subdomain.domain.tld>;  # CHANGE HERE

            location /.well-known/acme-challenge/ {
                root /var/www/certbot;
            }

            location / {
                return 301 https://$host$request_uri;
            }

            # Hide NGINX version for security reasons
            server_tokens off;
        }

        # HTTPS Configuration
        server {
            listen 443 ssl http2;
            listen [::]:443 ssl http2;  # Enable HTTP/2 and IPv6 support if you need
            server_name <subdomain.domain.tld>; # CHANGE HERE

            # Hide NGINX version
            server_tokens off;

            # SSL Certificates
            ssl_certificate /etc/letsencrypt/live/<subdomain.domain.tld>/fullchain.pem;  # CHANGE HERE
            ssl_certificate_key /etc/letsencrypt/live/<subdomain.domain.tld>/privkey.pem;  # CHANGE HERE
            ssl_trusted_certificate /etc/letsencrypt/live/<subdomain.domain.tld>/chain.pem;  # CHANGE HERE

            # Security: Stronger SSL Configuration
            ssl_session_cache shared:SSL:10m;  # Cache SSL sessions
            ssl_session_timeout 1d;  # Set session timeout to 1 day
            ssl_session_tickets off;  # Disable SSL session tickets

            # Use only modern SSL/TLS protocols (disables older insecure ones)
            ssl_protocols TLSv1.2 TLSv1.3;

            # Configure SSL ciphers (strong encryption algorithms)
            ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
            ssl_prefer_server_ciphers off;  # Allow clients to prefer their ciphers

            # Diffie-Hellman parameter for Perfect Forward Secrecy (PFS)
            ssl_dhparam /etc/ssl/certs/dhparam.pem;  # Generate with `sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048`

            # OCSP Stapling for improved SSL/TLS performance
            ssl_stapling on;
            ssl_stapling_verify on;

            # DNS resolver for OCSP
            resolver 1.1.1.1 1.0.0.1 valid=300s;
            resolver_timeout 5s;

            # some HTTP Security Headers
            add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;  # HSTS
            add_header X-Frame-Options "SAMEORIGIN" always;  # Prevent Clickjacking
            add_header X-Content-Type-Options "nosniff" always;  # Prevent MIME-type sniffing
            add_header X-XSS-Protection "1; mode=block" always;  # XSS Protection
            add_header Referrer-Policy "no-referrer" always;  # Better privacy control
            add_header Permissions-Policy "geolocation=(self), microphone=(), camera=()" always;  # Limit browser permissions

            # Reverse Proxy to Postiz application
            location / {
                proxy_pass http://localhost:5000;
                proxy_http_version 1.1;
                proxy_set_header Host $host;
                proxy_set_header X-Real-IP $remote_addr;
                proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                proxy_set_header X-Forwarded-Proto $scheme;

                # WebSocket Support
                proxy_set_header Upgrade $http_upgrade;
                proxy_set_header Connection "upgrade";
            }

            # Disable directory listing for security
            autoindex off;

            # Increase maximum post size to prevent 413 error with images larger than 2MB (changes max size to 100MB)
            client_max_body_size 100M;

            # Enable Gzip compression for better performance
            gzip on;
            gzip_comp_level 6;
            gzip_min_length 1100;
            gzip_buffers 4 32k;
            gzip_proxied any;
            gzip_types text/plain text/css text/javascript application/json application/xml image/svg+xml;

            # Optional Access/Error Logging
            access_log /var/log/nginx/<subdomain.domain.tld>.log; # CHANGE HERE
            error_log /var/log/nginx/<subdomain.domain.tld>.log; # CHANGE HERE
        }
        ```
      </Tab>

      <Tab title="Simple Config">
        ```nginx  theme={null}
        # HTTP -> HTTPS Redirection
        server {
            listen 80;
            server_name <subdomain.domain.tld>; # CHANGE HERE

            # Location for Let's Encrypt validation
            location /.well-known/acme-challenge/ {
                root /var/www/certbot;
            }

            return 301 https://$host$request_uri;  # Redirect all traffic to HTTPS
        }

        # HTTPS Configuration
        server {
            listen 443 ssl;
            server_name <subdomain.domain.tld>; # CHANGE HERE

            # SSL certificate
            ssl_certificate /etc/letsencrypt/live/<subdomain.domain.tld>/fullchain.pem; # CHANGE HERE
            ssl_certificate_key /etc/letsencrypt/live/<subdomain.domain.tld>/privkey.pem; # CHANGE HERE

            # Proxy requests to Postiz running on port 5000
            location / {
                proxy_pass http://localhost:5000;
                proxy_set_header Host $host;
                proxy_set_header X-Real-IP $remote_addr;
                proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                proxy_set_header X-Forwarded-Proto $scheme;

                # WebSocket Support
                proxy_set_header Upgrade $http_upgrade;
                proxy_set_header Connection "upgrade";
            }

            # Optional Access/Error Logging
            access_log /var/log/nginx/<subdomain.domain.tld>.log; # CHANGE HERE
            error_log /var/log/nginx/<subdomain.domain.tld>.log; # CHANGE HERE
        }
        ```
      </Tab>
    </Tabs>
  </Step>

  <Step title="Generate DH Parameters">
    (Needed if you are using the "robust" nginx config option)

    ```bash  theme={null}
    sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048
    ```
  </Step>

  <Step title="Test your Nginx configuration">
    ```bash  theme={null}
    sudo nginx -t
    ```
  </Step>

  <Step title="Automatic reload Nginx when SSL Certificates are renewed">
    ```bash  theme={null}
    echo -e '#!/bin/bash\nginx -t && systemctl reload nginx' | sudo tee /etc/letsencrypt/renewal-hooks/post/nginx-reload.sh && sudo chmod a+x /etc/letsencrypt/renewal-hooks/post/nginx-reload.sh
    ```
  </Step>

  <Step title="Reload Nginx">
    ```bash  theme={null}
    sudo systemctl reload nginx
    ```
  </Step>

  <Step title="Done!">
    Your Nginx reverse proxy is now running, securely serving your Postiz instance at your subdomain.domain.tld. 🎉
  </Step>
</Steps>


# Traefik + Docker Compose
Source: https://docs.postiz.com/reverse-proxies/traefik

Configure Traefik as a reverse proxy for Postiz

<Snippet file="earlydoc.mdx" />

This is a very popular configuration to get an easy reverse proxy up and running with Traefik and Docker Compose.

## Add Traefik to your Docker Compose file

```yaml  theme={null}
version: '3.8'
services:
  postiz:
    ##
    ## Include all the other configuration from the standard compose example.
    ##

    labels:
      # Router for main Postiz entrypoint (on port 5000)
      - "traefik.http.routers.postiz.rule=Host(`postiz.example.lan`)"  # Replace with your domain
      - "traefik.http.services.postiz.loadbalancer.server.port=5000"  # Internal port for postiz
      - "traefik.http.routers.postiz.entrypoints=websecure"  # Postiz requires HTTPS
      - "traefik.http.routers.postiz.tls=true"


  traefik:
    image: "traefik:v2.9"
    container_name: "traefik"
    command:
      #- "--log.level=DEBUG"
      - "--api.insecure=true"
      - "--api.dashboard=true"
      - "--providers.docker=true"
      - "--providers.docker.exposedbydefault=false"
      - "--entrypoints.websecure.address=:443"
    ports:
      - "443:443"
    volumes:
      - "/var/run/docker.sock:/var/run/docker.sock:ro"
    networks:
      - postiz-network
```


# Support
Source: https://docs.postiz.com/support

Get help with Postiz

Sometimes, things can go wrong, or you need some help!

Note that the Self Hosted version of Postiz is supported by the community in their
free time, on a best-efforts basis. Please post your question and be patient.

<CardGroup cols={2}>
  <Card title="Discord" icon="discord" href="https://discord.com/invite/sf7QjTcX37">
    Flexible chat, with screenshots and screen sharing, probably the best option for support.
  </Card>

  <Card title="GitHub Issues" icon="github" href="https://github.com/gitroomhq/postiz-app/issues/new/choose">
    Backup option if you are unable to use Discord.
  </Card>
</CardGroup>

## How to effectively ask for support

Try to follow this guide when asking for support, in this order; **Goal, Environment, Changes, Results**.

### Goal

Start off by explaining what you were trying to do:

* *I want to schedule a post on Reddit at 6pm*
* *I want to run in a Linux container on a Raspberry Pi*
* *I want to use a custom domain name*

### Environment

Share the relevant parts about your environment. Web App issues; Are you using Firefox, Chrome, etc? Installation/Other issues; a Mac, Linux, Windows, how did you install?

* *I'm using Firefox on Windows 10*
* *I'm using a Raspberry Pi 4 with Ubuntu 20.04, and Node version 18*
* *This is a new installation on a Mac*

### Changed

Most likely something has changed, what is it?

* *I updated my browser to the latest version and now ...*
* *I found a change in the latest version and now ...*
* *I think this used to work, but now\...*

### Results

What happened? What did you expect to happen?

* *I see a blank screen*
* *I see an error message*
* *I see a 404 page*

