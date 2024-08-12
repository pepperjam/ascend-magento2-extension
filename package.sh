extension=Network;

mage_root=../../../..;
ext_name=Pepperjam_${extension};
version_name=$(sed -n 's/.*setup_version="\([^"]*\).*/\1/p' etc/module.xml);
package_name=${ext_name,,}_${version_name}_m2;

# rm ${mage_root}/var/${package_name}.tgz;
# rm ${mage_root}/var/${package_name}.zip;

tar --exclude='package.sh' --exclude='.[^/]*' --exclude='README.md' \
    --transform 's,^,/app/code/Pepperjam/Network/,' \
    -czf ${mage_root}/var/${package_name}.tgz \
    -C ${mage_root}/app/code/Pepperjam/${extension} .;

zip -r -7q --exclude='package.sh' --exclude='.[^/]*' --exclude='README.md' \
    ${mage_root}/var/${package_name}.zip ./*;

# zip -r pepperjam_network-magento2-module-1.5.0.zip . -x '.git/*' -x 'package.sh' -x '.gitignore' -x 'README.md' -x '.idea/*'

echo Package: ${mage_root}/var/${package_name}.tgz;
echo Package: ${mage_root}/var/${package_name}.zip;
