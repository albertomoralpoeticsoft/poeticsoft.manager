import moment from 'moment'
moment.locale('es')
const { 
  registerPlugin 
} = wp.plugins
const { 
  PluginDocumentSettingPanel 
} = wp.editPost
const { 
  useEntityProp 
} = wp.coreData
const { 
  useEffect,
  useReducer
} = wp.element

import './main.scss'
import PublishNow from './editor/publishnow'
import PublishRules from './editor/publishrules'
import State from './editor/state'

const PoeticsoftPostSettingsTelegram = () => {

	const postType = wp.data
  .select('core/editor')
  .getCurrentPostType()

	if (postType !== 'post') {

		return null
	}

  const [ meta, setMeta ] = useEntityProp(
    'postType', 
    'post', 
    'meta'
  )
  const [ state, localDispatch ] = State(
    useReducer,
    setMeta,
    setMeta
  )
  
  useEffect(() => {

    fetch('/wp-json/poeticsoft/telegram/destinations')
    .then(
      result => result
      .json()
      .then(
        list => {

          const wheredefault = list.find(w => w.default)
          const whatdefault = state.publishwhat.find(w => w.default)
          
          localDispatch({
            destinations: list,
            publishnowwhat: whatdefault.value,
            publishnowwhere: [wheredefault.value]
          })
        }
      )
    )

    localDispatch({
      type: 'PUBLISH_RULES_LOAD',
      payload: meta.poeticsoft_post_publish_telegram_publishrules
    })

  }, [])

	return <PluginDocumentSettingPanel
    name="poeticsoft-post-settings-telegram"
    title="TELEGRAM"
    className="poeticsoft-post-settings-telegram"
  >
    <PublishNow 
      meta={ meta }
      setMeta={ setMeta }
      state={ state } 
      localDispatch={ localDispatch }
    />
    <PublishRules  
      meta={ meta }
      setMeta={ setMeta }
      state={ state } 
      localDispatch={ localDispatch }
    />
  </PluginDocumentSettingPanel>
}

registerPlugin(
  'poeticsoft-post-settings-telegram', 
  {
	  render: PoeticsoftPostSettingsTelegram,
    icon: null,
  }
)