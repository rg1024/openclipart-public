#!/bin/bash

# abort if not a valid xml file
xmlstarlet val -q "$1" || xmlstarlet val -e "$1" || exit 0

# verify that there is a default namespace
# todo: maybe we can select the default namespace like this? /*/namespace::*[name()='']
xmlstarlet sel -Q -t -i "/svg" -f "$1" && \
	xmlstarlet ed -L -s '/svg' -t attr -n xmlns -v 'http://www.w3.org/2000/svg' "$1"

# verify explicit namespaces in <svg>
xmlstarlet sel -Q -N svg="http://www.w3.org/2000/svg" -t -i '/svg:svg/namespace::svg' -f "$1" || \
	xmlstarlet ed -L -N svg="http://www.w3.org/2000/svg" -s '/svg:svg' -t attr -n xmlns:svg -v 'http://www.w3.org/2000/svg' "$1"
xmlstarlet sel -Q -t -i '/svg:svg/namespace::dc' -f "$1" || \
	xmlstarlet ed -L -s '/svg:svg' -t attr -n xmlns:dc -v 'http://purl.org/dc/elements/1.1/' "$1"
xmlstarlet sel -Q -t -i '/svg:svg/namespace::cc' -f "$1" || \
	xmlstarlet ed -L -s '/svg:svg' -t attr -n xmlns:cc -v 'http://web.resource.org/cc/' "$1"
xmlstarlet sel -Q -t -i '/svg:svg/namespace::rdf' -f "$1" || \
	xmlstarlet ed -L -s '/svg:svg' -t attr -n xmlns:rdf -v 'http://www.w3.org/1999/02/22-rdf-syntax-ns#' "$1"

# delete all metadata
xmlstarlet ed -L -d '/svg:svg/svg:metadata' "$1"

# metadata/RDF
xmlstarlet ed -L -s '/svg:svg' -t elem -n metadata -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata' -t elem -n rdf:RDF -v '' "$1"

# cc:Work
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF' -t elem -n cc:Work -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n dc:format -v 'image/svg+xml' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n dc:type -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:type' -t attr -n rdf:resource -v 'http://purl.org/dc/dcmitype/StillImage' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n cc:license -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/cc:license' -t attr -n rdf:resource -v 'http://creativecommons.org/licenses/publicdomain/' "$1"

xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n dc:publisher -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:publisher' -t elem -n cc:Agent -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:publisher/cc:Agent' -t attr -n rdf:about -v 'http://openclipart.org/' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work/dc:publisher/cc:Agent' -t elem -n dc:title -v 'Open Clip Art Library' "$1"

# cc:License
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF' -t elem -n cc:License -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License' -t attr -n rdf:about -v 'http://creativecommons.org/licenses/publicdomain/' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License' -t elem -n cc:permits -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License' -t elem -n cc:permits -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License' -t elem -n cc:permits -v '' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License/cc:permits[1]' -t attr -n rdf:resource -v 'http://creativecommons.org/ns#Reproduction' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License/cc:permits[2]' -t attr -n rdf:resource -v 'http://creativecommons.org/ns#Distribution' "$1"
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:License/cc:permits[3]' -t attr -n rdf:resource -v 'http://creativecommons.org/ns#DerivativeWorks' "$1"
