#!/bin/bash
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n dc:creator -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:creator' -t elem -n cc:Agent -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:creator/cc:Agent' -t elem -n dc:title -v "$2" "$1"
