#! /bin/bash
# GetText helper script. Finds translations & compile po files.
# author: Nicolas Pulido <nicolas.pulido@crazycake.cl>

# interrupt if error raises
set -e

# current path
PROJECT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_PATH="$(dirname "$PROJECT_PATH")"

# core directory
APP_PATH=$PROJECT_PATH"/app/"
APP_LANGS_PATH=$APP_PATH"langs/"
APP_CORE_PATH=$PROJECT_PATH"/../cc-phalcon/"
APP_VIEWS_CACHE_PATH=$PROJECT_PATH"/storage/cache/"

# translation filenames
MO_FILE="app.mo"
TEMP_FILE=".translations"

# help output
scriptHelp() {
	echo -e "\033[93m WebApp translations script\nValid actions:\033[0m"
	echo -e "\033[95m build: build po files in app folder. \033[0m"
		echo -e "\033[95m find: find for new translations in app folder. \033[0m"
	exit
}

# commands
case "$1" in

# compile and generate mo files
build)

	echo -e "\033[94mSearching for .po files in $APP_LANGS_PATH \033[0m"

	# generate .mo files in LC_MESSAGES subfolder for each lang code
	find $APP_LANGS_PATH -type f -name '*.po' | while read PO_FILE ; do

		CODE=`basename "$PO_FILE" .po`
		TARGET_DIR="$APP_LANGS_PATH$CODE/LC_MESSAGES"
		mkdir -p "$TARGET_DIR"
		echo -e "\033[94mCompiling language file: $CODE \033[0m"
		msgfmt -o "$TARGET_DIR/$MO_FILE" "$PO_FILE"
	done

	# task done!
	echo -e "\033[92mDone! \033[0m"
	;;

# search and generate pot files
find)

	echo -e "\033[95mSearching for keyword 'trans' in project files...  \033[0m"

	# find files (exclude some folders)
	find $APP_CORE_PATH $APP_PATH $APP_VIEWS_CACHE_PATH -type f -name '*.php' > $TEMP_FILE

	# generate pot file with xgettext
	xgettext -o $APP_LANGS_PATH"trans.pot" \
		-d $APP_PATH -L php --from-code=UTF-8 \
		-k'trans' -k'transPlural:1,2' \
		--copyright-holder="CrazyCake" \
		--package-name="crazycake" \
		--package-version='`date -u +"%Y-%m-%dT%H:%M:%SZ"`' \
		--no-wrap -f $TEMP_FILE

	# delete temp file
	rm $TEMP_FILE

	# merge po file
	find $APP_LANGS_PATH -mindepth 1 -maxdepth 1 -type d | while read CODE_DIR ; do

		cd "$CODE_DIR"

		CODE=`basename "$CODE_DIR"`

		if [ -f "$CODE".po ]; then
			echo -e "\033[94mUpdating new entries for lang code: $CODE  \033[0m"
			msgmerge -U "$CODE".po ../trans.pot
		else
			echo -e "\033[94mGenerating new entries for lang code: $CODE  \033[0m"
			msginit -i ../trans.pot --no-translator -l "$CODE"
		fi
	done

	# task done!
	echo -e "\033[92mDone! \033[0m"
	;;

#default
*)
	scriptHelp
	;;
esac
