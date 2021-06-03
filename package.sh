extension=Network;

mage_root=../../../..;
ext_name=Pepperjam_${extension};
version_name=$(sed -n 's/.*setup_version="\([^"]*\).*/\1/p' etc/module.xml);
package_name=${ext_name,,}_${version_name}_m2;

#rm ${mage_root}/var/${package_name}.tgz;
#rm ${mage_root}/var/${package_name}.zip;

tar --exclude='package.sh' --exclude='.[^/]*' \
    --transform 's,^,/app/code/Pepperjam/Network/,' \
    -czf ${mage_root}/var/${package_name}.tgz \
    -C ${mage_root}/app/code/Pepperjam/${extension} .;

zip -r -7q --exclude='package.sh' --exclude='.[^/]*' \
    ${mage_root}/var/${package_name}.zip ./*;

echo Package: ${mage_root}/var/${package_name}.tgz;
echo Package: ${mage_root}/var/${package_name}.zip;
