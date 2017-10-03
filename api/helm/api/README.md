# Deploy to a Kubernetes Cluster

## Prepare Your Cluster and Your Local Machine

1. Install [Kubernetes](https://kubernetes.io) locally and on your servers or create a cluster on [Google Container Engine](https://cloud.google.com/container-engine/)
2. Install [Helm](https://helm.sh/) locally and on your cluster following their documentation
3. Be sure to be connected to the right Kubernetes container
4. Update the Helm repo: `helm repo update`

## Create and Publish the Docker Images

1. Build the PHP and Nginx Docker images:

    docker build -t gcr.io/test-api-platform/php -t gcr.io/test-api-platform/php:latest api
    docker build -t gcr.io/test-api-platform/nginx -t gcr.io/test-api-platform/nginx:latest -f api/Dockerfile.nginx api
    docker build -t gcr.io/test-api-platform/varnish -t gcr.io/test-api-platform/varnish:latest -f api/Dockerfile.varnish api

2. Push your images to your Docker registry, example with [Google Container Registry](https://cloud.google.com/container-registry/):

    gcloud docker -- push gcr.io/test-api-platform/php
    gcloud docker -- push gcr.io/test-api-platform/nginx
    gcloud docker -- push gcr.io/test-api-platform/varnish

## Deploy

Deploy your API to the container:

    helm install ./api/helm/api --namespace=baz --name baz \
        --set php.repository=gcr.io/test-api-platform/php \
        --set nginx.repository=gcr.io/test-api-platform/nginx \
        --set secret=MyAppSecretKey \
        --set postgresql.postgresPassword=MyPgPassword \
        --set postgresql.persistence.enabled=true \
        --set corsAllowUrl='^https?://[a-z\]*\.mywebsite.com$'

If you prefer to use a managed DBMS like [Heroku Postgres](https://www.heroku.com/postgres) or
[Google Cloud SQL](https://cloud.google.com/sql/docs/postgres/) (recommended):

    helm install --name api ./api/helm/api \
        # ...
        --set postgresql.enabled=false \
        --set postgresql.url=pgsql://username:password@host/database?serverVersion=9.6

If you want to use a managed Varnish such as [Fastly](https://www.fastly.com) for the invalidation cache mechanism
provided by API Platform, don't forget to deploy a Varnish:

    helm install --name api ./api/helm/api \
        # ...
        --set varnish.enabled=false \
        --set varnish.url=https://myvarnish.com

Finally, build the `client` and `admin` JavaScript apps and [deploy them on a static
website hosting service](ttps://github.com/facebookincubator/create-react-app/blob/master/packages/react-scripts/template/README.md#deployment).

## Init the Database

    PHP_POD=$(kubectl --namespace=bar get pods -l app=php -o jsonpath="{.items[0].metadata.name}")
    kubectl --namespace=bar exec -it $PHP_POD -- bin/console doctrine:schema:create
