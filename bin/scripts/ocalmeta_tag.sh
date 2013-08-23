#!/bin/bash

xmlstarlet sel -Q -t -i '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:subject' -f "$1" || \
	xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n dc:subject -v '' "$1"

xmlstarlet sel -Q -t -i '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:subject/rdf:Bag' -f "$1" || \
	xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:subject' -t elem -n rdf:Bag -v '' "$1"

xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:subject/rdf:Bag' -t elem -n rdf:li -v "$2" "$1"
