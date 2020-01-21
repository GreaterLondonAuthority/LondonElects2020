#!/bin/sh

# Use docker-solr's precreate-core script to create core's before Solr is running
/opt/docker-solr/scripts/precreate-core solr /opt/docker-solr/configsets/$SOLR_DEFAULT_CONFIG_SET
