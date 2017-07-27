#! /bin/bash
# Core Task Runner Installer
# author: Nicolas Pulido <nicolas.pulido@crazycake.cl>

# interrupt if error raises
set -e
echo -e "\033[94mCore Package Installer \033[0m"

# project paths
PROJECT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_PATH="$(dirname "$PROJECT_PATH")"
# tools path
TOOLS_PATH=$PROJECT_PATH"/.tools/"
# destination path
DEST_PATH=$PROJECT_PATH"/core/"
# core source
CORE_PROJECT_NAME="cc-phalcon"
# symlink to core project
CORE_SRC_PATH="../$CORE_PROJECT_NAME/"
# sub-paths
CORE_SRC_TOOLS=$CORE_SRC_PATH"tools/"
CORE_SRC_VOLT=$CORE_SRC_PATH"volt/"

# main app bash file
ROOT_TOOL_FILES=("cli")

# check if cc-phalcon symlink is present
if [ ! -d $CORE_SRC_PATH ]; then
	echo -e "\033[31mCore project symlink folder not found ($CORE_SRC_PATH).\033[0m" && exit
fi

copyToolFiles() {

	echo -e "\033[94mCopying tool script files to $CORE_SRC_TOOLS... \033[0m"
	rm -rf $TOOLS_PATH
	mkdir -p $TOOLS_PATH
	# copy tool files
	find $CORE_SRC_TOOLS -maxdepth 1 -mindepth 1 -type f -print0 | while read -d $'\0' FILE; do

		# get file props
		FILENAME=$(basename "$FILE")

		echo -e "\033[96mCopying script file $FILENAME... \033[0m"

		# exclude main app script file (project folder)
		[[ " ${ROOT_TOOL_FILES[@]} " =~ " ${FILENAME} " ]] && cp $FILE "$PROJECT_PATH/" || cp $FILE "$TOOLS_PATH/"

	done
}

copyVoltFiles() {

	if [ -d $PROJECT_PATH"/ui/volt/" ]; then
		echo -e "\033[94mCopying volt files ... \033[0m"
		cp -r $CORE_SRC_VOLT $PROJECT_PATH"/ui/volt/"
	fi
}

buildCorePhar() {

	echo -e "\033[95mBuilding core phar file from $CORE_SRC_PATH... \033[0m"
	cd $CORE_SRC_PATH
	php box.phar build
	cp "$CORE_PROJECT_NAME.phar" "$DEST_PATH$CORE_PROJECT_NAME.phar"
}

# tasks
echo -e "\033[96mCore path: "$CORE_SRC_PATH" \033[0m"

# 1) tools
copyToolFiles

# 2) volt files
copyVoltFiles

# 3) php phar core builder
buildCorePhar

# task done!
echo -e "\033[92mDone! \033[0m"
